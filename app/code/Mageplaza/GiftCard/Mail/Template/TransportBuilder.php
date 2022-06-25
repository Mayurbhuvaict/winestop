<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Mail\Template;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder as DefaultBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Mail\EmailMessage;
use Mageplaza\GiftCard\Mail\EmailMessageFactory;
use Mageplaza\GiftCard\Mail\TransportFactory;
use Zend\Mime\Part;
use Zend_Mime;

/**
 * Class TransportBuilder
 * @package Mageplaza\GiftCard\Mail\Template
 */
class TransportBuilder extends DefaultBuilder
{
    /**
     * Attachment name
     */
    const ATTACHMENT_NAME = 'gift_card.pdf';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var TransportFactory
     */
    private $transportFactory;

    /**
     * @var EmailMessageFactory
     */
    private $emailMessageFactory;

    /**
     * TransportBuilder constructor.
     *
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param Data $helper
     * @param TransportFactory $transportFactory
     * @param EmailMessageFactory $emailMessageFactory
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        Data $helper,
        TransportFactory $transportFactory,
        EmailMessageFactory $emailMessageFactory
    ) {
        $this->helper              = $helper;
        $this->transportFactory    = $transportFactory;
        $this->emailMessageFactory = $emailMessageFactory;
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
    }

    /**
     * @param $attachFile
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @param string $filename
     *
     * @return $this|Part
     */
    public function addAttachment(
        $attachFile,
        $mimeType = 'application/pdf',
        $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = Zend_Mime::ENCODING_BASE64,
        $filename = self::ATTACHMENT_NAME
    ) {
        if ($this->helper->versionCompare('2.2.8')) {
            $attachment              = new Part($attachFile);
            $attachment->type        = $mimeType;
            $attachment->encoding    = $encoding;
            $attachment->disposition = $disposition;
            $attachment->filename    = $filename;

            return $attachment;
        }

        $this->message->createAttachment($attachFile, $mimeType, $disposition, $encoding, $filename);

        return $this;
    }

    /**
     * Get mail transport
     *
     * @return TransportInterface
     * @throws LocalizedException
     */
    public function getTransport()
    {
        if (!$this->helper->versionCompare('2.3.3')) {
            return parent::getTransport();
        }

        try {
            $this->prepareMessage();
            $mailTransport = $this->transportFactory->create(['message' => $this->prepareNewMessage()]);
        } finally {
            $this->reset();
        }

        return $mailTransport;
    }

    /**
     * @return EmailMessage
     */
    protected function prepareNewMessage()
    {
        $messageData = [
            'body'     => $this->message->getBody(),
            'subject'  => $this->message->getSubject(),
            'from'     => $this->message->getFrom(),
            'to'       => $this->message->getTo(),
            'cc'       => $this->message->getCc(),
            'bcc'      => $this->message->getBcc(),
            'replyTo'  => $this->message->getReplyTo(),
            'sender'   => $this->message->getSender(),
            'encoding' => $this->message->getEncoding()
        ];

        $this->message = $this->emailMessageFactory->create($messageData);

        return $this->message;
    }
}

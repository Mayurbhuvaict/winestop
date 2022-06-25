<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */






































namespace Aheadworks\AdvancedReports\Model\Email\Template;

use Aheadworks\AdvancedReports\Model\Email\Template\Message\Zend\Mime\MessageFactory as MimeMessageFactory;
use Aheadworks\AdvancedReports\Model\Email\Template\Message\Zend\Mime\PartFactory;
use Magento\Framework\Mail\MailMessageInterface;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Magento\Framework\Mail\Message as MagentoMessage;

/**
 * Class Message
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Template
 */
class Message extends MagentoMessage implements MailMessageInterface
{
    /**
     * @var PartFactory
     */
    protected $partFactory;

    /**
     * @var MimeMessageFactory
     */
    protected $mimeMessageFactory;

    /**
     * @var Part[]
     */
    protected $parts = [];

    /**
     * @var Message
     */
    protected $zendMessage;

    /**
     * Message type
     *
     * @var string
     */
    protected $messageType = self::TYPE_TEXT;

    /**
     * @param PartFactory $partFactory
     * @param MimeMessageFactory $mimeMessageFactory
     * @param string $charset
     */
    public function __construct(
        PartFactory $partFactory,
        MimeMessageFactory $mimeMessageFactory,
        $charset = 'utf-8'
    ) {
        $this->zendMessage = new \Zend\Mail\Message();
        $this->zendMessage->setEncoding($charset);
        $this->partFactory = $partFactory;
        $this->mimeMessageFactory = $mimeMessageFactory;
    }

    /**
     * @inheritdoc
     */
    public function setMessageType($type)
    {
        $this->messageType = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setBody($body)
    {
        if (is_string($body) && $this->messageType === MailMessageInterface::TYPE_HTML) {
            $body = $this->createHtmlMimeFromString($body);
        }
        $this->zendMessage->setBody($body);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->zendMessage->setSubject($subject);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->zendMessage->getSubject();
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->zendMessage->getBody();
    }

    /**
     * @inheritdoc
     */
    public function setFrom($fromAddress)
    {
        $this->setFromAddress($fromAddress, null);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFromAddress($fromAddress, $fromName = null)
    {
        $this->zendMessage->setFrom($fromAddress, $fromName);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTo($toAddress)
    {
        $this->zendMessage->addTo($toAddress);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addCc($ccAddress)
    {
        $this->zendMessage->addCc($ccAddress);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addBcc($bccAddress)
    {
        $this->zendMessage->addBcc($bccAddress);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyToAddress)
    {
        $this->zendMessage->setReplyTo($replyToAddress);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRawMessage()
    {
        return $this->zendMessage->toString();
    }

    /**
     * @inheritdoc
     */
    public function setBodyHtml($html)
    {
        $this->setMessageType(self::TYPE_HTML);

        return $this->setBody($html);
    }

    /**
     * @inheritdoc
     */
    public function setBodyText($text)
    {
        $this->setMessageType(self::TYPE_TEXT);

        return $this->setBody($text);
    }

    /**
     * @inheritdoc
     */
    private function createHtmlMimeFromString($htmlBody)
    {
        $htmlPart = new Part($htmlBody);
        $htmlPart->setCharset($this->zendMessage->getEncoding());
        $htmlPart->setType(Mime::TYPE_HTML);
        $mimeMessage = new \Zend\Mime\Message();
        $mimeMessage->addPart($htmlPart);

        return $mimeMessage;
    }

    /**
     * Add the attachment mime part to the message
     *
     * @param string $content
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @param null|string $filename
     * @return $this
     */
    public function setBodyAttachment(
        $content,
        $mimeType = Mime::TYPE_OCTETSTREAM,
        $disposition = Mime::DISPOSITION_ATTACHMENT,
        $encoding = Mime::ENCODING_BASE64,
        $filename = null
    ) {
        $attachmentPart = $this->partFactory->create();
        $attachmentPart
            ->setContent($content)
            ->setType($mimeType)
            ->setFileName($filename)
            ->setDisposition($disposition)
            ->setEncoding($encoding);

        $this->parts[] = $attachmentPart;

        return $this;
    }

    /**
     * Set parts to Zend message body
     *
     * @return $this
     */
    public function setPartsToBody()
    {
        $body = $this->zendMessage->getBody();
        foreach ($body->getParts() as $part) {
            $this->parts[] = $part;
        }
        $mimeMessage = $this->mimeMessageFactory->create();
        $mimeMessage->setParts($this->parts);
        $this->zendMessage->setBody($mimeMessage);
        unset($this->parts);

        return $this;
    }
}

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










































namespace Aheadworks\AdvancedReports\Model\Email\Template\TransportBuilder;

use Aheadworks\AdvancedReports\Model\Email\Template\TransportBuilderInterface;
use Aheadworks\AdvancedReports\Model\Email\Template\MessageFactory;
use Magento\Framework\Mail\Template\TransportBuilder as FrameworkTransportBuilder;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Exception\MailException;

/**
 * Class VersionPriorTo233
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Template\TransportBuilder
 */
class VersionPriorTo233 extends FrameworkTransportBuilder implements TransportBuilderInterface
{
    /**
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param MessageFactory $customMessageFactory
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        MessageFactory $customMessageFactory
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        $this->message = $customMessageFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function addAttachment(
        $body,
        $filename = null,
        $mimeType = \Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = \Zend_Mime::ENCODING_BASE64
    ) {
        $this->message->setBodyAttachment($body, $mimeType, $disposition, $encoding, $filename);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws MailException
     */
    public function setFrom($from)
    {
        if (method_exists($this, 'setFromByScope')) {
            return $this->setFromByScope($from, null);
        } else {
            return parent::setFrom($from);
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();
        $this->message->setPartsToBody();

        return $this;
    }
}

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

































namespace Aheadworks\AdvancedReports\Model\Email;

use Aheadworks\AdvancedReports\Model\Email\Template\TransportBuilderInterface;
use Aheadworks\AdvancedReports\Model\Email\Template\TransportBuilder\Factory as TransportBuilderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;

/**
 * Class Sender
 *
 * @package Aheadworks\EventTickets\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilderFactory
     */
    private $transportBuilderFactory;

    /**
     * @param TransportBuilderFactory $transportBuilderFactory
     */
    public function __construct(
        TransportBuilderFactory $transportBuilderFactory
    ) {
        $this->transportBuilderFactory = $transportBuilderFactory;
    }

    /**
     * Send email message
     *
     * @param EmailMetadataInterface $emailMetadata
     * @return bool
     * @throws LocalizedException
     * @throws MailException
     */
    public function send($emailMetadata)
    {
        /** @var TransportBuilderInterface $transportBuilder */
        $transportBuilder = $this->transportBuilderFactory->create();
        $transportBuilder
            ->setTemplateIdentifier($emailMetadata->getTemplateId())
            ->setTemplateOptions($emailMetadata->getTemplateOptions())
            ->setTemplateVars($emailMetadata->getTemplateVariables())
            ->setFrom(['name' => $emailMetadata->getSenderName(), 'email' => $emailMetadata->getSenderEmail()])
            ->addTo($emailMetadata->getRecipientEmail(), $emailMetadata->getRecipientName());

        $attachments = $emailMetadata->getAttachments() ? : [];
        foreach ($attachments as $attachment) {
            $transportBuilder->addAttachment($attachment->getAttachment(), $attachment->getFileName());
        }
        $transportBuilder->getTransport()->sendMessage();

        return true;
    }
}

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



























































































































































namespace Aheadworks\AdvancedReports\Model\Service;

use Aheadworks\AdvancedReports\Api\ScheduledEmailManagementInterface;
use Aheadworks\AdvancedReports\Model\Email\Report\Processor as EmailReportProcessor;
use Aheadworks\AdvancedReports\Model\Email\Sender as EmailSender;
use Psr\Log\LoggerInterface;
use Aheadworks\AdvancedReports\Model\Email\Report\ConfigProvider;
use Aheadworks\AdvancedReports\Model\Email\Report\Scheduler;

/**
 * Class ScheduledEmailService
 *
 * @package Aheadworks\AdvancedReports\Model\Service
 */
class ScheduledEmailService implements ScheduledEmailManagementInterface
{
    /**
     * @var EmailSender
     */
    private $sender;

    /**
     * @var EmailReportProcessor
     */
    private $emailProcessor;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EmailSender $sender
     * @param EmailReportProcessor $emailProcessor
     * @param LoggerInterface $logger
     * @param ConfigProvider $configProvider
     * @param Scheduler $scheduler
     */
    public function __construct(
        EmailSender $sender,
        EmailReportProcessor $emailProcessor,
        LoggerInterface $logger,
        ConfigProvider $configProvider,
        Scheduler $scheduler
    ) {
        $this->sender = $sender;
        $this->emailProcessor = $emailProcessor;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
        $this->scheduler = $scheduler;
    }

    /**
     * @inheritdoc
     */
    public function processScheduledEmails()
    {
        $config = $this->configProvider->getConfig();
        if (!$config) {
            return false;
        }

        if (!$this->scheduler->isScheduledToBeSent($config)) {
            return false;
        }

        try {
            $emailMetadataList = $this->emailProcessor->process($config);
            if (is_array($emailMetadataList)) {
                foreach ($emailMetadataList as $emailMetadata) {
                    $this->sender->send($emailMetadata);
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }

        return true;
    }
}

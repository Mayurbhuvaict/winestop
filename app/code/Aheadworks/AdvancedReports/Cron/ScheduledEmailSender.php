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
































































































































































































































namespace Aheadworks\AdvancedReports\Cron;

use Aheadworks\AdvancedReports\Api\ScheduledEmailManagementInterface;

/**
 * Class ScheduledEmailSender
 *
 * @package Aheadworks\AdvancedReports\Cron
 */
class ScheduledEmailSender
{
    /**
     * @var ScheduledEmailManagementInterface
     */
    private $scheduledEmailService;

    /**
     * @param ScheduledEmailManagementInterface $scheduledEmailService
     */
    public function __construct(
        ScheduledEmailManagementInterface $scheduledEmailService
    ) {
        $this->scheduledEmailService = $scheduledEmailService;
    }

    /**
     * Process scheduled emails
     */
    public function execute()
    {
        $this->scheduledEmailService->processScheduledEmails();
    }
}

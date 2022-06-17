<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Cron;


use Magento\Framework\App\MaintenanceMode;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Model\ConfigProvider;
use Mirasvit\SeoAudit\Repository\JobRepository;
use Mirasvit\SeoAudit\Service\JobService;
use Psr\Log\LoggerInterface;

class JobCron
{
    private $config;

    private $jobService;

    private $jobRepository;

    private $maintenanceMode;

    private $logger;

    public function __construct(
        ConfigProvider $config,
        JobService $jobService,
        JobRepository $jobRepository,
        MaintenanceMode $maintenanceMode,
        LoggerInterface $logger
    ) {
        $this->config          = $config;
        $this->jobService      = $jobService;
        $this->jobRepository   = $jobRepository;
        $this->maintenanceMode = $maintenanceMode;
        $this->logger          = $logger;
    }

    public function execute(): void
    {
        if ($this->maintenanceMode->isOn()) {
            $this->logger->notice('Maintenance mode enabled. SEO Audit will not crawl URLs');

            return;
        }

        if (!$this->config->shouldRunAudit()) {
            return;
        }

        $runningJob = $this->jobRepository->getRunningJob();

        if (!$runningJob) {
            $runningJob = $this->jobService->startJob();
        }

        $now       = new \Zend_Date(microtime(true), \Zend_Date::TIMESTAMP);
        $startedAt = new \Zend_Date(strtotime($runningJob->getStartedAt()), \Zend_Date::TIMESTAMP);

        // start a new job every day
        if ($now->toString('d') !== $startedAt->toString('d')) {
            $this->jobService->startJob();
        }

        $this->jobService->runJob();
    }
}

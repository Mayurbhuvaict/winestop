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


use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Repository\JobRepository;

class CleanupCron
{
    private $jobRepository;

    public function __construct(
        JobRepository $jobRepository
    ) {
        $this->jobRepository = $jobRepository;
    }

    public function execute(): void
    {
        /** @var JobInterface $lastFinishedJob */
        $lastFinishedJob = $this->jobRepository->getCollection()
            ->addFieldToFilter(JobInterface::STATUS, JobInterface::STATUS_FINISHED)
            ->getLastItem();

        if (!$lastFinishedJob || !$lastFinishedJob->getId()) {
            return;
        }

        $finishedAt = strtotime($lastFinishedJob->getFinishedAt());

        $threshold = $finishedAt - 3 * 24 * 60 * 60;
        $threshold = date('Y-m-d H:i:s', $threshold);

        $oldJobs = $this->jobRepository->getCollection()
            ->addFieldToFilter(JobInterface::STARTED_AT, ['lteq' => $threshold]);

        $ids = [];

        foreach ($oldJobs as $job) {
            $ids[] = $job->getId();
        }

        if (!count($ids)) {
            return;
        }

        $ids = implode(',', $ids);

        $resource   = $oldJobs->getResource();
        $connection = $resource->getConnection();

        $query = "DELETE FROM {$resource->getTable(CheckResultInterface::TABLE_NAME)} WHERE job_id IN ({$ids})";

        $connection->query($query);
    }
}

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


namespace Mirasvit\SeoAudit\Repository;


use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Model\JobFactory;
use Mirasvit\SeoAudit\Model\ResourceModel\Job\Collection;
use Mirasvit\SeoAudit\Model\ResourceModel\Job\CollectionFactory;

class JobRepository
{
    private $jobFactory;

    private $collectionFactory;

    private $entityManager;

    public function __construct(
        JobFactory $jobFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->jobFactory      = $jobFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
    }

    public function getCollection(): Collection
    {
        return $this->collectionFactory->create();
    }

    public function create(): JobInterface
    {
        return $this->jobFactory->create();
    }

    public function get(int $id): ?JobInterface
    {
        $job = $this->create();
        $job = $this->entityManager->load($job, $id);

        return $job->getId() ? $job : null;
    }

    public function getRunningJob(): ?JobInterface
    {
        $runningJob = $this->getCollection()
            ->addFieldToFilter(JobInterface::STATUS, JobInterface::STATUS_PROCESSING)
            ->getLastItem();

        return $runningJob && $runningJob->getId() ? $runningJob : null;
    }

    public function save(JobInterface $job): JobInterface
    {
        return $this->entityManager->save($job);
    }

    public function delete(JobInterface $job): bool
    {
        $this->entityManager->delete($job);

        return true;
    }
}

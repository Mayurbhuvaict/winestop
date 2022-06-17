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
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Check\AbstractCheck;
use Mirasvit\SeoAudit\Model\CheckResultFactory;
use Mirasvit\SeoAudit\Model\ResourceModel\CheckResult\Collection;
use Mirasvit\SeoAudit\Model\ResourceModel\CheckResult\CollectionFactory;
use Mirasvit\SeoAudit\Service\UrlService;

class CheckResultRepository
{
    private $jobCheckFactory;

    private $urlService;

    private $collectionFactory;

    private $entityManager;

    /** @var AbstractCheck[] */
    private $pool;

    public function __construct(
        CheckResultFactory $jobCheckFactory,
        UrlService $urlService,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager,
        array $pool = []
    ) {
        $this->jobCheckFactory   = $jobCheckFactory;
        $this->urlService        = $urlService;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
        $this->pool              = $pool;
    }

    public function getCollection(): Collection
    {
        return $this->collectionFactory->create();
    }

    public function create(): CheckResultInterface
    {
        return $this->jobCheckFactory->create();
    }

    public function get(int $id): ?CheckResultInterface
    {
        $jobCheck = $this->create();
        $jobCheck = $this->entityManager->load($jobCheck, $id);

        return $jobCheck->getId() ? $jobCheck : null;
    }

    public function save(CheckResultInterface $jobCheck): CheckResultInterface
    {
        return $this->entityManager->save($jobCheck);
    }

    public function delete(CheckResultInterface $jobCheck): bool
    {
        $this->entityManager->delete($jobCheck);

        return true;
    }

    /**
     * @return AbstractCheck[]
     */
    public function getAllowedChecks(UrlInterface $url): array
    {
        $allowedChecks = [];

        foreach ($this->pool as $check) {
            if ($this->urlService->isExternalUrl($url->getUrl()) && !$check->isAllowedForExternal()) {
                continue;
            }

            if (
                in_array($url->getType(), $check->getAllowedTypes())
                || in_array('all', $check->getAllowedTypes())
            ) {
                $allowedChecks[] = $check;
            }
        }

        return $allowedChecks;
    }

    /**
     * @return AbstractCheck[]
     */
    public function getAllChecks(): array
    {
        return $this->pool;
    }

    public function getCheckInstanceByIdentifier(string $identifier): ?AbstractCheck
    {
        return isset($this->pool[$identifier])
            ? $this->pool[$identifier]
            : null;
    }
}

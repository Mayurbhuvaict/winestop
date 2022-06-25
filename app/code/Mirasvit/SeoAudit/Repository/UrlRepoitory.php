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
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Model\UrlFactory;
use Mirasvit\SeoAudit\Model\ResourceModel\Url\Collection;
use Mirasvit\SeoAudit\Model\ResourceModel\Url\CollectionFactory;

class UrlRepoitory
{
    private $urlFactory;

    private $collectionFactory;

    private $entityManager;

    public function __construct(
        UrlFactory $urlFactory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->urlFactory        = $urlFactory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
    }

    public function getCollection(): Collection
    {
        return $this->collectionFactory->create();
    }

    public function getUnprocessedUrlsCollection(): Collection
    {
        return $this->getCollection()
            ->addFieldToFilter(UrlInterface::STATUS, UrlInterface::STATUS_PENDING);
    }

    public function getUrlsCollectionForCheck(int $jobId): Collection
    {
        return $this->getCollection()
            ->addFieldToFilter(UrlInterface::STATUS, UrlInterface::STATUS_CRAWLED)
            ->addFieldToFilter(UrlInterface::JOB_ID, $jobId);
    }

    public function create(): UrlInterface
    {
        return $this->urlFactory->create();
    }

    public function get(int $id): ?UrlInterface
    {
        $url = $this->create();
        $url = $this->entityManager->load($url, $id);

        return $url->getId() ? $url : null;
    }

    public function getByUrl(string $url): ?UrlInterface
    {
        /** @var UrlInterface $crawledUrl */
        $crawledUrl = $this->getCollection()
            ->addFieldToFilter(UrlInterface::URL_HASH, sha1($url))
            ->getFirstItem();

        return $crawledUrl && $crawledUrl->getId() ? $crawledUrl : null;
    }

    public function save(UrlInterface $url): UrlInterface
    {
        return $this->entityManager->save($url);
    }

    public function delete(UrlInterface $url): bool
    {
        $related = $this->getCollection()
            ->addFieldToFilter(UrlInterface::PARENT_IDS, ['finset' => $url->getId()]);

        /** @var UrlInterface $r */
        foreach ($related as $r) {
            $updatedParentIds = array_diff($r->getParentIds(), [$url->getId()]);

            $r->setParentIds($updatedParentIds);

            $this->save($r);
        }

        $this->entityManager->delete($url);

        return true;
    }
}

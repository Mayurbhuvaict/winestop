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



namespace Mirasvit\SeoContent\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\SeoContent\Api\Data\RewriteInterface;
use Mirasvit\SeoContent\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoContent\Model\RewriteFactory;
use Mirasvit\SeoContent\Model\ResourceModel\Rewrite\CollectionFactory;

class RewriteRepository implements RewriteRepositoryInterface
{
    /**
     * @var RewriteFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RewriteRepository constructor.
     * @param RewriteFactory $factory
     * @param CollectionFactory $collectionFactory
     * @param EntityManager $entityManager
     */
    public function __construct(
        RewriteFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @return RewriteInterface[]|\Mirasvit\SeoContent\Model\ResourceModel\Rewrite\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     * @return bool|false|RewriteInterface|\Mirasvit\SeoContent\Model\Rewrite|mixed
     */
    public function get($id)
    {
        $rewrite = $this->create();
        $rewrite = $this->entityManager->load($rewrite, $id);

        if (!$rewrite->getId()) {
            return false;
        }

        return $rewrite;
    }

    /**
     * @param RewriteInterface $rewrite
     * @return RewriteInterface|object
     * @throws \Exception
     */
    public function save(RewriteInterface $rewrite)
    {
        return $this->entityManager->save($rewrite);
    }

    /**
     * @param RewriteInterface $rewrite
     * @return bool
     * @throws \Exception
     */
    public function delete(RewriteInterface $rewrite)
    {
        $this->entityManager->delete($rewrite);

        return true;
    }
}

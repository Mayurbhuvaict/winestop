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



namespace Mirasvit\Seo\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterfaceFactory;
use Mirasvit\Seo\Model\ResourceModel\CanonicalRewrite\CollectionFactory;
use Mirasvit\Seo\Api\Data\CanonicalRewriteStoreInterface;
use Mirasvit\Core\Service\SerializeService;

class CanonicalRewriteRepository implements CanonicalRewriteRepositoryInterface
{
    /**
     * @var CanonicalRewriteInterfaceFactory
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
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * CanonicalRewriteRepository constructor.
     * @param CanonicalRewriteInterfaceFactory $factory
     * @param CollectionFactory $collectionFactory
     * @param EntityManager $entityManager
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        CanonicalRewriteInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $model = $this->create();

        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : false;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CanonicalRewriteInterface $model)
    {
        $model = $this->prepareRuleData($model);

        $result = $this->entityManager->save($model);

        $this->saveStore($model);


        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CanonicalRewriteInterface $model)
    {
        return $this->entityManager->delete($model);
    }

    /**
     * @param CanonicalRewriteInterface $model
     *
     * @return CanonicalRewriteInterface
     */
    protected function prepareRuleData($model)
    {
        // Serialize conditions
        if ($model->getConditions()
            && ($ruleData = $model->getData('rule'))) {
            /** @var mixed $model */
            $model->loadPost($ruleData);
            $conditions = SerializeService::encode($model->getConditions()->asArray());
            $model->setConditionsSerialized($conditions);
        }

        // Serialize actions
        if ($model->getActions()) {
            $actions = SerializeService::encode($model->getActions()->asArray());
            $model->setActionsSerialized($actions);
        }

        return $model;
    }

    /**
     * @param CanonicalRewriteInterface $model
     *
     * @return void
     */
    protected function saveStore($model)
    {
        if ($model->getData('store_ids')) {
            $connection = $this->resource->getConnection();
            $condition = $connection->quoteInto(
                CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID . ' = ?',
                $model->getId()
            );
            $connection->delete($this->resource->getTableName(CanonicalRewriteStoreInterface::TABLE_NAME), $condition);
            foreach ((array)$model->getData('store_ids') as $store) {
                $storeArray = [
                    CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID => $model->getId(),
                    CanonicalRewriteStoreInterface::STORE_ID => $store,
                ];
                $connection->insert(
                    $this->resource->getTableName(CanonicalRewriteStoreInterface::TABLE_NAME),
                    $storeArray
                );
            }
        }
    }
}

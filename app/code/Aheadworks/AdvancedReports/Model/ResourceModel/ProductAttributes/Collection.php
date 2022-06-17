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





















































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\ProductAttributes;

use Aheadworks\AdvancedReports\Model\ResourceModel\AbstractPeriodBasedCollection;
use Magento\Framework\DataObject;
use Aheadworks\AdvancedReports\Model\ResourceModel\ProductAttributes as ResourceProductAttributes;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\ProductAttributes
 */
class Collection extends AbstractPeriodBasedCollection
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param AttributeRepositoryInterface $attributeRepository
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->metadataPool = $metadataPool;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(DataObject::class, ResourceProductAttributes::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()], []);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->columns($this->getColumns(true));
        parent::_renderFiltersBefore();
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns($addRate = false)
    {
        $rateField = $this->getRateField($addRate);
        return [
            'order_items_count' => 'SUM(COALESCE(main_table.order_items_count, 0))',
            'subtotal' => 'SUM(COALESCE(main_table.subtotal' . $rateField . ', 0))',
            'tax' => 'SUM(COALESCE(main_table.tax' . $rateField . ', 0))',
            'total' => 'SUM(COALESCE(main_table.total' . $rateField . ', 0))',
            'invoiced' => 'SUM(COALESCE(main_table.invoiced' . $rateField . ', 0))',
            'refunded' => 'SUM(COALESCE(main_table.refunded' . $rateField . ', 0))',
        ];
    }

    /**
     * Add attribute filter to collection
     *
     * @param array $conditions
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addAttributeFilter(array $conditions)
    {
        $this->getSelect()
            ->joinLeft(
                ['e' => 'catalog_product_entity'],
                'main_table.product_id = e.entity_id',
                []
            );

        $conditionSql = '';
        foreach ($conditions as $key => $condition) {
            if ($key > 0) {
                $conditionSql .= ' ' . $condition['operator'] . ' ';
            }
            $conditionSql .=
                '(' . $this->getAttributeConditionSql($condition['attribute'], $condition['condition']) . ')';
        }
        if (!empty($conditionSql)) {
            $this->conditionsForGroupBy[] = [
                'field' => '(' . $conditionSql . ')',
                'condition' => []
            ];
        }

        return $this;
    }

    /**
     * Get condition sql for the attribute
     *
     * @param string $attributeCode
     * @param array $condition
     * @return string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getAttributeConditionSql($attributeCode, $condition)
    {
        /* @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attribute = $this->attributeRepository->get('catalog_product', $attributeCode);

        if ($attribute->getBackend()->isStatic()) {
            $conditionSql = $this->prepareSqlCondition('e.' . $attributeCode, $condition);
        } else {
            $linkField = $this->getCatalogLinkField();
            $table = $attribute->getBackendTable();
            $tableAlias = 'at_' . $attribute->getAttributeCode();
            $tableAliasDefault = '';
            $onDefaultField = '';

            if (!$this->getFlag($tableAlias . '_joined')) {
                $storeConditions = $tableAlias . '.store_id = 0';
                if (!$attribute->isScopeGlobal()) {
                    $storeIds = $this->getCustomFilterValue(self::STORE_IDS_FILTER_KEY);
                    if (is_array($storeIds)) {
                        $storeConditions = $tableAlias . '.store_id IN (' . implode($storeIds) . ')';
                        $tableAliasDefault = $tableAlias . '_d';
                        $defaultStoreConditions = $tableAliasDefault . '.store_id = 0';
                    }
                }
                $this->getSelect()
                    ->joinLeft(
                        [$tableAlias => $table],
                        'e.' . $linkField . ' = ' . $tableAlias . '.' . $linkField
                        . ' AND ' . $tableAlias . '.attribute_id = ' . $attribute->getId()
                        . ' AND ' . $storeConditions,
                        []
                    );
                if ($tableAliasDefault) {
                    $this->getSelect()
                        ->joinLeft(
                            [$tableAliasDefault => $table],
                            'e.' . $linkField . ' = ' . $tableAliasDefault . '.' . $linkField
                            . ' AND ' . $tableAliasDefault . '.attribute_id = ' . $attribute->getId()
                            . ' AND ' . $defaultStoreConditions,
                            []
                        );
                    $onDefaultField = $tableAliasDefault . '.value';
                }
                $this->setFlag($tableAlias . '_joined', true);
            }
            $conditionSql = $this->prepareSqlCondition(
                $tableAlias . '.value',
                $condition,
                $onDefaultField
            );
        }
        return $conditionSql;
    }

    /**
     * Retrieve sql condition
     *
     * @param string $field
     * @param array $condition
     * @param string $onDefaultField
     * @return string $onDefaultField
     */
    private function prepareSqlCondition($field, $condition, $onDefaultField = '')
    {
        $prefix = '';
        foreach ($condition as $key => &$value) {
            switch ($key) {
                case 'like':
                case 'nlike':
                    $value = '%' . $value . '%';
                    break;
                case 'in':
                case 'nin':
                    $value = implode(',', array_map('trim', explode(',', $value)));
                    break;
                case 'not_finset':
                    $condition['finset'] = $value;
                    $prefix = 'NOT ';
                    unset($condition['not_finset']);
                    break;
            }
        }
        $conditionSql = $prefix . $this->_getConditionSql(
            $this->getConnection()->quoteIdentifier($field),
            $condition
        );

        if ($onDefaultField) {
            $onDefaultConditionSql = $this->_getConditionSql(
                $this->getConnection()->quoteIdentifier($onDefaultField),
                $condition
            );
            $conditionSql = 'COALESCE(' . $conditionSql . ',' . $prefix . $onDefaultConditionSql . ')';
        }

        return $conditionSql;
    }

    /**
     * Retrieve catalog link field
     *
     * @return string
     * @throws \Exception
     */
    private function getCatalogLinkField()
    {
        return $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
    }
}

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
























































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

use Aheadworks\AdvancedReports\Model\Flag;
use Aheadworks\AdvancedReports\Model\FlagFactory;
use Aheadworks\AdvancedReports\Model\Config;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Indexer\Table\StrategyInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module\ExpressionBuilder as ModuleExpressionBuilder;

/**
 * Class AbstractResource
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractResource extends \Magento\Indexer\Model\ResourceModel\AbstractResource
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var Flag
     */
    private $reportsFlag;

    /**
     * @var ModuleExpressionBuilder
     */
    protected $moduleExpressionBuilder;

    /**
     * @var string
     */
    private $catalogLinkField;

    /**
     * @var string
     */
    private $updatedAtFlag;

    /**
     * @var int
     */
    private $offset;

    /**
     * Other discounts expressions
     *
     * @var array
     */
    private $otherDiscounts = [
        'Magento_CustomerBalance' => 'COALESCE(main_table.base_customer_balance_amount, 0.0)',
        'Magento_GiftCard' => 'COALESCE(main_table.base_gift_cards_amount, 0.0)',
        'Magento_Reward' => 'COALESCE(main_table.base_reward_currency_amount, 0.0)',
        'Aheadworks_StoreCredit' => 'ABS(COALESCE(main_table.base_aw_store_credit_amount, 0.0))',
        'Aheadworks_RewardPoints' => 'ABS(COALESCE(main_table.base_aw_reward_points_amount, 0.0))',
        'Aheadworks_Giftcard' => 'ABS(COALESCE(main_table.base_aw_giftcard_amount, 0.0))',
        'Aheadworks_Raf' => 'ABS(COALESCE(main_table.base_aw_raf_amount, 0.0))'
    ];

    /**
     * @param Context $context
     * @param Config $config
     * @param TimezoneInterface $localeDate
     * @param StrategyInterface $tableStrategy
     * @param MetadataPool $metadataPool
     * @param AttributeRepositoryInterface $attributeRepository
     * @param FlagFactory $reportsFlagFactory
     * @param ModuleExpressionBuilder $moduleExpressionBuilder
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        Config $config,
        TimezoneInterface $localeDate,
        StrategyInterface $tableStrategy,
        MetadataPool $metadataPool,
        AttributeRepositoryInterface $attributeRepository,
        FlagFactory $reportsFlagFactory,
        ModuleExpressionBuilder $moduleExpressionBuilder,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
        $this->config = $config;
        $this->localeDate = $localeDate;
        $this->metadataPool = $metadataPool;
        $this->attributeRepository = $attributeRepository;
        $this->reportsFlag = $reportsFlagFactory->create();
        $this->moduleExpressionBuilder = $moduleExpressionBuilder;
    }

    /**
     * Performs report
     *
     * @return null
     */
    abstract protected function process();

    /**
     * Reindex all
     *
     * @return $this
     * @throws \Exception
     */
    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->clearTemporaryIndexTable();
        $this->beginTransaction();
        try {
            $this->process();
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        $this->syncData();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearTemporaryIndexTable()
    {
        $this->getConnection()->truncateTable($this->getIdxTable());
    }

    /**
     * Safe insert from select to avoid lock Magento tables during reindex
     *
     * @param Select $select
     * @param string $table
     * @param array $fields
     */
    protected function safeInsertFromSelect($select, $table, $fields)
    {
        $connection = $this->getConnection();
        $connection->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        $connection->query($select->insertFromSelect($table, $fields));
    }

    /**
     * Get period with Timezone offset from default config
     *
     * @param string $field
     * @param int|null $store
     * @return string
     */
    protected function getPeriod($field, $store = null)
    {
        return 'DATE(DATE_ADD(' . $field . ', INTERVAL ' . $this->getOffset($store) . ' SECOND))';
    }

    /**
     * Get catalog link field
     *
     * @return string
     * @throws \Exception
     */
    protected function getCatalogLinkField()
    {
        if (!$this->catalogLinkField) {
            $this->catalogLinkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
        }
        return $this->catalogLinkField;
    }

    /**
     * Set disable_staging_preview part to select
     *
     * @return null
     */
    protected function disableStagingPreview()
    {
        if ($this->isPartExists('disable_staging_preview')) {
            $this->getConnection()->select()->setPart('disable_staging_preview', true);
        }
    }

    /**
     * Add filter by created date
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $tableAlias
     * @return null
     */
    protected function addFilterByCreatedAt($select, $tableAlias)
    {
        return $select->where($tableAlias . '.created_at <= "' . $this->getUpdatedAtFlag() . '"');
    }

    /**
     * Retrieve sql query to calculate the sum of bundle items
     *
     * @return \Zend_Db_Expr
     */
    protected function getBundleItemsPrice()
    {
        $select = $this->getConnection()->select()
            ->from(
                ['bundle_items' => $this->getTable('sales_order_item')],
                [
                    'parent_item_id',
                    'order_id',
                    new \Zend_Db_Expr('Sum(`bundle_items`.`price`) as price'),
                    new \Zend_Db_Expr('Sum(`bundle_items`.`base_cost`) as items_cost')
                ]
            )->join(
                ['bundle' => $this->getTable('sales_order_item')],
                'bundle_items.parent_item_id = bundle.item_id AND bundle.product_type IN ("bundle")',
                []
            )->group(['bundle_items.parent_item_id', 'bundle_items.order_id']);

        return new \Zend_Db_Expr('(' . $select . ')') ;
    }

    /**
     * Is exists part key in select
     *
     * @param string $partKey
     * @return bool
     */
    private function isPartExists($partKey)
    {
        try {
            $this->getConnection()->select()->getPart($partKey);
        } catch (\Zend_Db_Select_Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get updated date flag
     *
     * @param int|null $store
     * @return string
     */
    protected function getUpdatedAtFlag()
    {
        if (!$this->updatedAtFlag) {
            $flag = $this->reportsFlag->setReportFlagCode(Flag::AW_AREP_STATISTICS_FLAG_CODE)->loadSelf();
            $this->updatedAtFlag = $flag->getLastUpdate();
        }
        return $this->updatedAtFlag;
    }

    /**
     * Get offset
     *
     * @param int|null $store
     * @return int
     */
    private function getOffset($store = null)
    {
        if (!$this->offset) {
            $timezone = $this->localeDate->scopeDate($store)->format('e');
            $this->offset = (new \DateTimeZone($timezone))->getOffset(new \DateTime());
        }
        return $this->offset;
    }

    /**
     * Get manufacturer attribute
     *
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute|null
     */
    protected function getManufacturerAttribute()
    {
        $manufacturerCode = $this->config->getManufacturerAttribute();
        /* @var $manufacturerAttr \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        try {
            $manufacturerAttr = $this->attributeRepository->get('catalog_product', $manufacturerCode);
        } catch (\Exception $e) {
            return null;
        }

        return $manufacturerAttr;
    }

    /**
     * Get manufacturer attribute
     *
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute|null
     */
    protected function getTaxClassAttribute()
    {
        try {
            /* @var $taxClassAttribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            $taxClassAttribute = $this->attributeRepository->get('catalog_product', 'tax_class_id');
        } catch (\Exception $e) {
            return null;
        }

        return $taxClassAttribute;
    }

    /**
     * Get other discounts expression
     *
     * @return string
     */
    protected function getOtherDiscountsExpression()
    {
        foreach ($this->otherDiscounts as $moduleName => $expression) {
            $this->moduleExpressionBuilder->addExpression($moduleName, $expression);
        }

        $otherDiscountsExpression = $this->moduleExpressionBuilder
            ->setGroupExpression('SUM')
            ->setDefaultEmptyExpression('0.0')
            ->create();

        return $otherDiscountsExpression;
    }
}

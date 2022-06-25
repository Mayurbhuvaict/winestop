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



























































































































































































































namespace Aheadworks\AdvancedReports\Setup;

use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as StatisticsProcessor;
use Aheadworks\AdvancedReports\Model\CustomerSales\Range;
use Aheadworks\AdvancedReports\Model\CustomerSales\RangeFactory;
use Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Range as RangeResource;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\DB\Select;
use Magento\Reports\Model\Event;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

/**
 * Class UpgradeData
 *
 * @package Aheadworks\AdvancedReports\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Number of records to import per one query
     */
    const RECORDS_COUNT_PER_RUN = 1000;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var RangeFactory
     */
    private $rangeFactory;

    /**
     * @var RangeResource
     */
    private $rangeResource;

    /**
     * @var ConfigInterface
     */
    private $coreConfig;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param IndexerRegistry $indexerRegistry
     * @param RangeFactory $rangeFactory
     * @param RangeResource $rangeResource
     * @param ConfigInterface $coreConfig
     * @param JsonSerializer $serializer
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        RangeFactory $rangeFactory,
        RangeResource $rangeResource,
        ConfigInterface $coreConfig,
        JsonSerializer $serializer
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->rangeFactory = $rangeFactory;
        $this->rangeResource = $rangeResource;
        $this->coreConfig = $coreConfig;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $arepIndex = $this->indexerRegistry->get(StatisticsProcessor::INDEXER_ID);
            $arepIndex->setScheduled(true);
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $this->importProductViewsData($setup);
        }

        if (version_compare($context->getVersion(), '2.3.0', '<')) {
            $this->addCustomerGroupToProductViewsData($setup);
            $arepIndex = $this->indexerRegistry->get(StatisticsProcessor::INDEXER_ID);
            $arepIndex->invalidate();
        }

        if (version_compare($context->getVersion(), '2.5.0', '<')) {
            $this->addDefaultRanges();
            $arepIndex = $this->indexerRegistry->get(StatisticsProcessor::INDEXER_ID);
            $arepIndex->invalidate();
        }

        if (version_compare($context->getVersion(), '2.5.1', '<')) {
            $arepIndex = $this->indexerRegistry->get(StatisticsProcessor::INDEXER_ID);
            $arepIndex->invalidate();
        }

        if (version_compare($context->getVersion(), '2.7.0', '<')) {
            $arepIndex = $this->indexerRegistry->get(StatisticsProcessor::INDEXER_ID);
            $arepIndex->invalidate();
        }
    }

    /**
     * Import products page views
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function importProductViewsData(ModuleDataSetupInterface $setup)
    {
        /** @var Select $visitorsSelect */
        $visitorsSelect = clone $setup->getConnection()->select();
        $visitorsSelect
            ->reset()
            ->from(['cv' => $setup->getTable('customer_visitor')])
            ->where('cv.customer_id IS NOT NULL')
            ->group(['cv.customer_id', 'day'])
            ->order('day ASC')
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(
                [
                    'visitor_id' => 'MIN(cv.visitor_id)',
                    'customer_id' => 'cv.customer_id',
                    'day' => 'DATE(cv.last_visit_at)'
                ]
            );

        $columns = [
            'logged_at' => 'MIN(re.logged_at)',
            'product_id' => 're.object_id',
            'visitor_id' => 'COALESCE(cv.visitor_id, re.subject_id)',
            'store_id' => 're.store_id'
        ];
        /** @var Select $select */
        $select = clone $setup->getConnection()->select();
        $select
            ->reset()
            ->from(['re' => $setup->getTable('report_event')])
            ->joinLeft(
                ['cv' => $visitorsSelect],
                'cv.customer_id = re.subject_id AND cv.day = DATE(re.logged_at) AND re.subtype = 0',
                []
            )
            ->where('re.event_type_id = ' . Event::EVENT_PRODUCT_VIEW)
            ->group(['product_id', 'visitor_id', 'store_id'])
            ->order('logged_at ASC')
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns($columns);

        $recordsCount = $this->getSize($setup, $select);
        $pagesCount = ceil($recordsCount / self::RECORDS_COUNT_PER_RUN);

        for ($page = 1; $page <= $pagesCount; $page++) {
            $select->limitPage($page, self::RECORDS_COUNT_PER_RUN);
            $query = $select->insertFromSelect($setup->getTable('aw_arep_log_product_view'), array_keys($columns));
            $setup->getConnection()->query($query);
        }
    }

    /**
     * Get records count of sql query
     *
     * @param ModuleDataSetupInterface $setup
     * @param Select $select
     * @return int
     */
    private function getSize(ModuleDataSetupInterface $setup, Select $select)
    {
        $sizeSelect = clone $select;
        $sizeSelect
            ->reset()
            ->from(['log' => $select])
            ->reset(Select::COLUMNS)
            ->columns(
                [
                    'size' => 'count(*)'
                ]
            );

        $size = $setup->getConnection()->fetchOne($sizeSelect);

        return (int)$size;
    }

    /**
     * Add customer group to product views data
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addCustomerGroupToProductViewsData(ModuleDataSetupInterface $setup)
    {
        /** @var Select $customersSelect */
        $customersSelect = clone $setup->getConnection()->select();
        $customersSelect
            ->reset()
            ->joinLeft(
                ['cv' => $setup->getTable('customer_visitor')],
                'cv.visitor_id = lpv.visitor_id',
                ['customer_id' => 'cv.customer_id']
            )
            ->joinLeft(
                ['ce' => $setup->getTable('customer_entity')],
                'ce.entity_id = cv.customer_id',
                ['customer_group_id' => 'ce.group_id']
            );
        $query = $setup->getConnection()->updateFromSelect(
            $customersSelect,
            ['lpv' => $setup->getTable('aw_arep_log_product_view')]
        );
        $setup->getConnection()->query($query);
    }

    /**
     * Add default ranges
     *
     * @return void
     * @throws AlreadyExistsException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addDefaultRanges()
    {
        $rangesData = [
            ['range_from' => 0.00, 'range_to' => 99.99],
            ['range_from' => 100.00, 'range_to' => 249.99],
            ['range_from' => 250.00, 'range_to' => 999.99],
            ['range_from' => 1000, 'range_to' => null]
        ];

        foreach ($rangesData as $rangeData) {
            /** @var Range $range */
            $range = $this->rangeFactory->create();
            $range
                ->setWebsiteId(0)
                ->setRangeFrom($rangeData['range_from'])
                ->setRangeTo($rangeData['range_to']);
            $this->rangeResource->save($range);
        }

        $this->coreConfig->saveConfig(
            'aw_advancedreports/general/ranges',
            $this->serializer->serialize($rangesData),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );
    }
}

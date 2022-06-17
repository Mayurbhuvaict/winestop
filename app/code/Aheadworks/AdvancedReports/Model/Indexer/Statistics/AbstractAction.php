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























































































































































































namespace Aheadworks\AdvancedReports\Model\Indexer\Statistics;

use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\Factory as IndexerStatisticsFactory;
use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;
use Aheadworks\AdvancedReports\Model\Flag;
use Aheadworks\AdvancedReports\Model\FlagFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class AbstractAction
 *
 * @package Aheadworks\AdvancedReports\Model\Indexer\Statistics
 */
abstract class AbstractAction
{
    /**
     * @var IndexerStatisticsFactory
     */
    private $indexerStatisticsFactory;

    /**
     * @var Flag
     */
    private $reportsFlag;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var []
     */
    private $resourceModelNames = [
        'sales_overview' => Statistics\SalesOverview::class,
        'sales_overview_coupon_code' => Statistics\SalesOverviewByCouponCode::class,
        'conversion' => Statistics\Conversion::class,
        'product_conversion' => Statistics\ProductConversion::class,
        'abandoned_carts' => Statistics\AbandonedCarts::class,
        'product_performance' => Statistics\ProductPerformance::class,
        'product_performance_category' => Statistics\ProductPerformanceByCategory::class,
        'product_performance_coupon_code' => Statistics\ProductPerformanceByCouponCode::class,
        'product_performance_manufacturer' => Statistics\ProductPerformanceByManufacturer::class,
        'product_variant_performance' => Statistics\ProductVariantPerformance::class,
        'sales_detailed' => Statistics\SalesDetailed::class,
        'location' => Statistics\Location::class,
        'coupon_code' => Statistics\CouponCode::class,
        'payment_type' => Statistics\PaymentType::class,
        'manufacturer' => Statistics\Manufacturer::class,
        'category' => Statistics\Category::class,
        'product_attributes' => Statistics\ProductAttributes::class,
        'customer_sales' => Statistics\CustomerSales::class
    ];

    /**
     * @param Statistics\Factory $indexerStatisticsFactory
     * @param FlagFactory $reportsFlagFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        IndexerStatisticsFactory $indexerStatisticsFactory,
        FlagFactory $reportsFlagFactory,
        DateTime $dateTime
    ) {
        $this->indexerStatisticsFactory = $indexerStatisticsFactory;
        $this->reportsFlag = $reportsFlagFactory->create();
        $this->dateTime = $dateTime;
    }

    /**
     * Execute action for given ids
     *
     * @param []|int $ids
     * @return void
     */
    abstract public function execute($ids);

    /**
     * Reindex all reports
     *
     * @return void
     */
    public function reindexAll()
    {
        $this->setFlagData(Flag::AW_AREP_STATISTICS_FLAG_CODE);
        foreach ($this->resourceModelNames as $resourceModelName) {
            $this->indexerStatisticsFactory->create($resourceModelName)->reindexAll();
        }
    }

    /**
     * Saves flag
     *
     * @param string $code
     * @param mixed $value
     * @return $this
     */
    private function setFlagData($code, $value = null)
    {
        $this->reportsFlag->setReportFlagCode($code)->unsetData()->loadSelf();
        if ($value !== null) {
            $this->reportsFlag->setFlagData($value);
        }
        // Touch last_update
        $this->reportsFlag->setLastUpdate($this->dateTime->gmtDate());
        $this->reportsFlag->save();

        return $this;
    }
}

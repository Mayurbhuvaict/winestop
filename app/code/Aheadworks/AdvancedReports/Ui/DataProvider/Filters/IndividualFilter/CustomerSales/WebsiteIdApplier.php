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

















































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Filters\IndividualFilter\CustomerSales;

use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\FilterApplierInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Range as CustomerSalesRangeResource;

/**
 * Class WebsiteIdApplier
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Filters\IndividualFilter\ReportSettings
 */
class WebsiteIdApplier implements FilterApplierInterface
{
    /**
     * @var CustomerSalesRangeResource
     */
    private $customerSalesRangeResource;

    /**
     * @param CustomerSalesRangeResource $customerSalesRangeResource
     */
    public function __construct(CustomerSalesRangeResource $customerSalesRangeResource)
    {
        $this->customerSalesRangeResource = $customerSalesRangeResource;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($collection, $filterPool)
    {
        $websiteId = $filterPool->getFilter('store')->getWebsiteId();
        if (!$this->customerSalesRangeResource->hasConfigValuesForWebsite($websiteId)) {
            $websiteId = 0;
        }

        $collection->setWebsiteId($websiteId);
    }
}

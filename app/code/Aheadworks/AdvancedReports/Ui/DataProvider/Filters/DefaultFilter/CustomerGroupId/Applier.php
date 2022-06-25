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






















































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\CustomerGroupId;

use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\FilterApplierInterface;
use Magento\Customer\Model\GroupManagement;

/**
 * Class Applier
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\CustomerGroupId
 */
class Applier implements FilterApplierInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply($collection, $filterPool)
    {
        $customerGroupId = $filterPool->getFilter('customer_group')->getValue();
        if ($customerGroupId != GroupManagement::CUST_GROUP_ALL) {
            $collection->addCustomerGroupFilter($customerGroupId);
        }
    }
}

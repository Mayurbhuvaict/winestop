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

















































































































































































































namespace Aheadworks\AdvancedReports\Model\CustomerSales;

use Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Range as RangeResource;

/**
 * Class Range
 * @package Aheadworks\AdvancedReports\Model\CustomerSales
 */
class Range extends \Magento\Framework\Model\AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RangeResource::class);
    }
}

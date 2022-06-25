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



























































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor;

/**
 * Class CustomerSalesCustomer
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor
 */
class CustomerSalesCustomer extends Field
{
    /**
     * {@inheritdoc}
     */
    protected function isEqualsValues($rowValue, $compareRowValue)
    {
        if (empty($rowValue['customer_id']) || empty($compareRowValue['customer_id'])) {
            return false;
        }
        if ((!empty($rowValue['customer_id']) && !empty($compareRowValue['customer_id'])
                && $rowValue['customer_id'] == $compareRowValue['customer_id'])
            || $rowValue['customer_email'] == $compareRowValue['customer_email']) {
            return true;
        }

        return false;
    }
}

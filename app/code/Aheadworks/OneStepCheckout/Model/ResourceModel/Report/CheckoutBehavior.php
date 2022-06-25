<?php
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Report;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CheckoutBehavior
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\Report
 */
class CheckoutBehavior extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_osc_checkout_data_completeness', 'id');
    }
}

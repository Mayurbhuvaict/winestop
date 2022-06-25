<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Method;

class MassInactivate extends Method
{
    public function execute()
    {
        return $this->_modifyStatus(0);
    }
}

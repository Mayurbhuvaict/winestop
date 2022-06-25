<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Method;

use Meetanshi\ShippingRates\Controller\Adminhtml\Method;

class MassActivate extends Method
{
    public function execute()
    {
        return $this->_modifyStatus(1);
    }
}

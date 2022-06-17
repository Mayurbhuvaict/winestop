<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Method;

class NewAction extends Method
{
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}

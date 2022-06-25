<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Rate;

class Index extends Rate
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $html = $resultRedirect->getLayout()->createBlock('Meetanshi\ShippingRates\Block\Adminhtml\Rates')->toHtml();
        $this->getResponse()->setBody($html);
    }
}

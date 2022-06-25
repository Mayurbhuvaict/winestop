<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Method;

class Index extends Method
{
    public function execute()
    {
        $this->_view->loadLayout();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Sales::shipping_table');
        $resultPage->addBreadcrumb(__('Shipping Table Rates'), __('Shipping Table Rates'));
        $resultPage->addBreadcrumb(__('Manage Shipping Table Rates'), __('Shipping Table Rates'));
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Table Rates'));
        $this->_addContent($this->_view->getLayout()->createBlock('\Meetanshi\ShippingRates\Block\Adminhtml\Method'));
        $this->_view->renderLayout();
    }
}

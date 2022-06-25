<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magecomp\Extrafee\Controller\Adminhtml\Category;

class Index extends Category
{
    public function execute()
    {
        $this->_view->loadLayout();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Sales::shipping_table');
        $resultPage->addBreadcrumb(__('Shipping Table Category'), __('Shipping Table Category'));
        $resultPage->addBreadcrumb(__('Manage Shipping Table Category'), __('Shipping Table Category'));
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Table Category'));
        $this->_addContent($this->_view->getLayout()->createBlock('\Magecomp\Extrafee\Block\Adminhtml\Category'));
        $this->_view->renderLayout();
    }
}

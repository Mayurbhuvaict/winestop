<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magecomp\Extrafee\Controller\Adminhtml\Category;

class Edit extends Category
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->categoryFactory->create()->load($id);
        
        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        } else {
            $this->prepareForEdit($model);
        }

        $this->registry->register('shippingrates_category', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Shipping Table Category') : __('New Shipping Table Category'),
            $id ? __('Edit Shipping Table Category') : __('New Shipping Table Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Table Rates'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Shipping Table Category'));

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Sales::shipping_table')
            ->addBreadcrumb(__('Shipping Table Rates'), __('Shipping Table Rates'))
            ->addBreadcrumb(__('Manage Shipping Table Rates'), __('Manage Shipping Table Rates'));
        return $resultPage;
    }
}

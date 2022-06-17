<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Method;

class Edit extends Method
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->methodFactory->create()->load($id);

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

        $this->registry->register('shippingrates_method', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Shipping Table Rates') : __('New Shipping Table Rate'),
            $id ? __('Edit Shipping Table Rates') : __('New Shipping Table Rate')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Table Rates'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Shipping Table Rate'));

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

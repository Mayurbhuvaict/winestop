<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Method;

class Delete extends Method
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->methodFactory->create()->load($id);

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage($this->__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Shipping method has been successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/');
        return;
    }
}

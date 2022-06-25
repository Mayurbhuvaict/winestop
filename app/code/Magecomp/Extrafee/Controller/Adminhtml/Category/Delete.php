<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magecomp\Extrafee\Controller\Adminhtml\Category;

class Delete extends Category
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->categoryFactory->create()->load($id);

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage($this->__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Shipping category has been successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/');
        return;
    }
}

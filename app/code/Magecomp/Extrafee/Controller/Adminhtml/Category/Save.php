<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magecomp\Extrafee\Controller\Adminhtml\Category;

class Save extends Category
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->categoryFactory->create();
        $data = $this->getRequest()->getPostValue();
        
        if ($data) {
            $model->setData($data);
            $model->setId($id);
            try {
                $this->prepareForSave($model);
                $model->save();
        

                $this->backendSession->setFormData(false);

                $msg = __('Shipping category have been successfully saved');
                $this->messageManager->addSuccessMessage($msg);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                } else {
                    $this->_redirect('*/*');
                    return;
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $id]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find a record to save'));
        $this->_redirect('*/*');
        return;
    }
}

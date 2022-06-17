<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Method;

class Save extends Method
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->methodFactory->create();
        $data = $this->getRequest()->getPostValue();
        $files = $this->getRequest()->getFiles();

        if ($data) {
            $model->setData($data);
            $model->setId($id);
            try {
                $this->prepareForSave($model);

                $model->save();

                if ($model->getData('import_clear')) {
                    $this->rateFactory->create()->deleteBy($model->getId());
                }

                if (!empty($files['import_file']['name'])) {
                    $fileName = $files['import_file']['tmp_name'];
                    ini_set('auto_detect_line_endings', 1);

                    $errors = $this->rateFactory->create()->import($model->getId(), $fileName);
                    foreach ($errors as $err) {
                        $this->messageManager->addErrorMessage($err);
                    }
                }
                $this->backendSession->setFormData(false);

                $msg = __('Shipping Matrix rates have been successfully saved');
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

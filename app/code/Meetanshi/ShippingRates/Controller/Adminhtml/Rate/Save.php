<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Rate;

class Save extends Rate
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->rateFactory->create()->load($id);
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            $this->messageManager->addErrorMessage(__('Unable to find a rate to save'));
            $this->_redirect('shippingrates/method/index');
            return;
        }

        try {
            $methodId = $model->getMethodId();
            if (!$methodId) {
                $methodId = $data['method_id'];
            }
            $zipFrom = $this->helper->getNumericZip($data['zip_from']);
            $zipTo = $this->helper->getNumericZip($data['zip_to']);
            $data['num_zip_from'] = $zipFrom['district'];
            $data['num_zip_to'] = $zipTo['district'];
            $model->setData($data)->setId($id);
            $model->save();

            $this->backendSession->setFormData(false);

            $msg = __('Rate has been successfully saved');
            $this->messageManager->addSuccessMessage($msg);
            $this->_redirect('shippingrates/method/edit', ['id' => $methodId, 'tab' => 'rates']);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('This rate already exist!');
            $this->backendSession->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $id, 'mid' => $methodId]);
            return;
        }
    }
}

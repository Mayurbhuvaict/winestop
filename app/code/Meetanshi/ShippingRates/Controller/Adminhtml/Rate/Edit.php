<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\ShippingRates\Controller\Adminhtml\Rate;

class Edit extends Rate
{
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->rateFactory->create()->load($id);

        $methodId = (int)$this->getRequest()->getParam('mid');

        if (!$methodId) {
            $methodId = $model->getMethodId();
        }

        if (!$methodId && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('Record #%1 does not exist', $id));
            $this->_redirect('shippingrates/method/index');
            return;
        }
        $data = $this->backendSession->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        if ($methodId && !$model->getId()) {
            $model->setMethodId($methodId);
            $model->setWeightFrom('0');
            $model->setQtyFrom('0');
            $model->setPriceFrom('0');
            $model->setWeightTo('99999999');
            $model->setQtyTo('99999999');
            $model->setPriceTo('99999999');
        }

        $this->registry->register('shippingrates_rate', $model);
        $this->_view->loadLayout();
        $this->_addContent($this->_view->getLayout()->createBlock('\Meetanshi\ShippingRates\Block\Adminhtml\Rate\Edit'));
        $this->_view->renderLayout();
    }
}

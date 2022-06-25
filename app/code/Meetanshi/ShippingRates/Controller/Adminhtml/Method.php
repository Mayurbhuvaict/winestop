<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\ShippingRates\Model\MethodFactory;
use Meetanshi\ShippingRates\Model\RateFactory;

abstract class Method extends Action
{
    protected $_title = 'Custom Shipping Methods';
    protected $_modelName = 'method';
    protected $methodFactory;
    protected $rateFactory;
    protected $registry;
    protected $resultPageFactory;
    protected $context;
    protected $backendSession;

    public function __construct(MethodFactory $methodFactory, Registry $registry, Context $context, ForwardFactory $resultForwardFactory, RateFactory $rateFactory, PageFactory $resultPageFactory, Session $backendSession)
    {
        parent::__construct($context);
        $this->methodFactory = $methodFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->rateFactory = $rateFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->backendSession = $backendSession;
    }

    public function prepareForSave($model)
    {
        $fields = ['stores', 'cust_groups'];
        foreach ($fields as $value) {
            $val = $model->getData($value);
            $model->setData($value, '');
            if (is_array($val)) {
                $model->setData($value, ',' . implode(',', $val) . ',');
            }
        }
        return true;
    }

    public function prepareForEdit($model)
    {
        $fields = ['stores', 'cust_groups'];
        foreach ($fields as $value) {
            $val = $model->getData($value);
            if (!is_array($val)) {
                $model->setData($value, explode(',', $val));
            }
        }
        return true;
    }

    protected function _modifyStatus($status)
    {
        $ids = $this->getRequest()->getParam('methods');
        if ($ids && is_array($ids)) {
            try {
                $this->methodFactory->create()->massChangeStatus($ids, $status);
                $message = __('Total of %1 record(s) have been updated.', count($ids));
                $this->messageManager->addSuccessMessage($message);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select method(s).'));
        }

        return $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
        return true;
    }
}

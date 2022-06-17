<?php

namespace Meetanshi\ShippingRates\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Meetanshi\ShippingRates\Model\RateFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\Auth\Session;
use Meetanshi\ShippingRates\Helper\Data;

abstract class Rate extends Action
{
    protected $rateFactory;
    protected $resultPageFactory;
    protected $registry;
    protected $context;
    protected $backendSession;
    protected $helper;

    public function __construct(RateFactory $rateFactory, Registry $registry, Context $context, ForwardFactory $resultForwardFactory, PageFactory $resultPageFactory, Session $backendSession, Data $helper)
    {
        parent::__construct($context);
        $this->rateFactory = $rateFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->backendSession = $backendSession;
        $this->helper = $helper;
    }

    protected function initModel()
    {
        $model = $this->rateFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('shippingrates_rate', $model);
        return $model;
    }

    protected function _isAllowed()
    {
        return true;
    }
}

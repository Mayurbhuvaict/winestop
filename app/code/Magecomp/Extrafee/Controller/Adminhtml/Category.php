<?php

namespace Magecomp\Extrafee\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magecomp\Extrafee\Model\CategoryFactory;

abstract class Category extends Action
{
    protected $_title = 'Custom Shipping Categorys';
    protected $_modelName = 'Category';
    protected $categoryFactory;
    protected $rateFactory;
    protected $registry;
    protected $resultPageFactory;
    protected $context;
    protected $backendSession;

    public function __construct(CategoryFactory $categoryFactory, Registry $registry, Context $context, ForwardFactory $resultForwardFactory, PageFactory $resultPageFactory, Session $backendSession)
    {
        parent::__construct($context);
        $this->categoryFactory = $categoryFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;
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
        $ids = $this->getRequest()->getParam('category');
        if ($ids && is_array($ids)) {
            try {
                $this->categoryFactory->create()->massChangeStatus($ids, $status);
                $message = __('Total of %1 record(s) have been updated.', count($ids));
                $this->messageManager->addSuccessMessage($message);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select Category(s).'));
        }

        return $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
        return true;
    }
}

<?php
/**
 * Webkul Software
 *
 * @category    Webkul
 * @package     Webkul_OutOfStockNotification
 * @author      Webkul
 * @copyright   Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license     https://store.webkul.com/license.html
 */
namespace Webkul\OutOfStockNotification\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Webkul\OutOfStockNotification\Helper\Data;

/*
 * MassNotified Class
 */
class MassNotified extends \Magento\Backend\App\Action
{
    /**
     *
     * @var \Webkul\OutOfStockNotification\Model\Product
     */
    private $productModel;
   
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Webkul\OutOfStockNotification\Helper\Data
     */
    private $helperData;

    /**
     * customer model
     * @var \Magento\Customer\Model\Customer
     */
    private $customerModel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;
    
    /**
     *
     * @param Action\Context                                                    $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                $scopeConfig
     * @param \Magento\Customer\Model\CustomerFactory                           $customerModel
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory    $productFactory
     * @param Data                                                              $helperData
     * @param \Webkul\OutOfStockNotification\Model\Product                      $productModel
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerFactory $customerModel,
        \Magento\Catalog\Model\Product $productFactory,
        Data $helperData,
        \Webkul\OutOfStockNotification\Model\Product $productModel,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
    
        $this->scopeConfig = $scopeConfig;
        $this->productFactory = $productFactory;
        $this->helperData = $helperData;
        $this->customerModel = $customerModel;
        $this->productModel = $productModel;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $name = $this->scopeConfig->getValue('notificationsettings/settings/adminname', $storeScope);
        $email = $this->scopeConfig->getValue('notificationsettings/settings/adminemail', $storeScope);
        if ($name == "" || $email== "") {
            $this->messageManager->addError(__('Please complete the configuration.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('outofstocknotification/product/preorderrequests');
        }
        $notificationModel = $this->productModel;
        $model = $this->filter;
        $collection = $model->getCollection($notificationModel->getCollection());
        try {
            foreach ($collection as $product) {
                    $customerName = "";
                    $product->setStatus(1);
                    $this->saveProduct($product);
                    $senderInfo = [];
                    $receiverInfo = [];
                    $productCollection = $this->productFactory->load($product->getProductId());
                    $customer=$this->customerModel->create()
                                        ->getCollection()
                                        ->addFieldToFilter('email', ['eq'=>$product->getEmail()]);
                foreach ($customer as $customerInfo) {
                    $customerName=$customerInfo->getFirstname();
                }
                if (empty($customerName)) {
                    $custName = __("buyer");
                } else {
                    $custName = $customerName;
                }
                    
                    $receiverInfo = [
                        'name' => $custName,
                        'email' => $product->getEmail(),
                     ];
                    $senderInfo = [
                        'name' => $name,
                        'email' => $email,
                     ];

                    $emailTempVariables = [];
                    $emailTempVariables['customerName'] = $custName;
                    $emailTempVariables['productName'] = $productCollection->getName();
                    $emailTempVariables['productUrl'] = $this->helperData
                                                             ->getFrontendProductUrl($product->getProductId());
                    $emailTempVariables['adminName'] = $name;
                    if ($productCollection['quantity_and_stock_status']['is_in_stock']!=false) {
                        $this->helperData->productInStockNotification(
                            $emailTempVariables,
                            $senderInfo,
                            $receiverInfo
                        );
                    }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->messageManager->addSuccess(__('Status changed successfully.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('outofstocknotification/product/preorderrequests');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Backend::admin');
    }

    /**
     * save product
     *
     * @param \Webkul\OutOfStockNotification\Model\Product $product
     * @return void
     */
    private function saveProduct($product)
    {
        $product->save();
    }
}

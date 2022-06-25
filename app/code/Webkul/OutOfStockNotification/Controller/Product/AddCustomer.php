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

namespace Webkul\OutOfStockNotification\Controller\Product;

use Magento\Framework\App\Action\Context;
use Webkul\OutOfStockNotification\Helper\Data;

/**
 * AddCustomer Class
 */
class AddCustomer extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Webkul\OutOfStockNotification\Model\ProductFactory
     */
    private $productFactory;

    /**
     * Magento\Framework\App\Config\ScopeConfigInterface
     *
     */
    private $scopeConfig;

    /**
     * \Magento\Customer\Model\CustomerFactory
     */
    private $customerModel;

    /**
     * \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJson;

    /**
     * @param Context                                             $context
     * @param Data                                                $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory    $resultJson
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param \Magento\Customer\Model\CustomerFactory             $customerModel
     * @param \Magento\Catalog\Model\Product                      $product
     * @param \Webkul\OutOfStockNotification\Model\ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJson,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerFactory $customerModel,
        \Magento\Catalog\Model\Product $product,
        \Webkul\OutOfStockNotification\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productFactory = $productFactory;
        $this->resultJson = $resultJson;
        $this->customerModel = $customerModel;
        $this->product = $product;
        $this->scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_storeManager = $storeManager;
        // $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $storeEmail = $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                $storeScope
            );
            $storeName  = $this->scopeConfig->getValue(
                'trans_email/ident_general/name',
                $storeScope
            );
            $senderName = $this->scopeConfig->getValue(
                'notificationsettings/settings/adminname',
                $storeScope
            );
            $senderEmail = $this->scopeConfig->getValue(
                'notificationsettings/settings/adminemail',
                $storeScope
            );
            $getNotification = $this->scopeConfig->getValue(
                'notificationsettings/settings/adminemailoptions',
                $storeScope
            );
            $notifyCustomer = $this->scopeConfig->getValue(
                'notificationsettings/emailsettings/product_instock_registration_status',
                $storeScope
            );
            $notifyAdmin = $this->scopeConfig->getValue(
                'notificationsettings/emailsettings/admin_notification_status',
                $storeScope
            );
            
            if ($this->getRequest()->isPost()) {
                return $this->refractor(
                    $senderName,
                    $senderEmail,
                    $storeName,
                    $storeEmail,
                    $notifyAdmin,
                    $notifyCustomer
                );
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            
        } catch (\Exception $e) {
            $result = ['error' => __("Email not sent.")];
            return $this->resultJson->create()->setData($result);
        }
    }

    /**
     * save model
     *
     * @param object $model
     * @return void
     */
    private function saveModel($model)
    {
        $model->save();
    }

    private function refractor($senderName, $senderEmail, $storeName, $storeEmail, $notifyAdmin, $notifyCustomer)
    {
        $senderInfoCustomer = [];
        $receiverInfoCustomer = [];
        $senderInfoAdmin = [];
        $receiverInfoAdmin = [];
        $productIds = [];
        $id=$this->getRequest()->getParam('productId');
        $productCollection = $this->product->load($id);
        $email = $this->getRequest()->getParam('email');

        // getting website code for current website
        $websiteCode = $this->_storeManager->getStore()->getWebsite()->getCode();
        
        $model = $this->productFactory->create();
        $mCollection = $model->getCollection();
        if ($productCollection->getTypeId() == "configurable") {
            $collection = $model->getCollection()
                ->addFieldToFilter("email", ['eq' => $email])
                ->addFieldToFilter("product_id", ['eq' => $id])
                ->addFieldToFilter("status", ['eq' => 0]);
            if ($collection->getSize() > 0) {
                $result = ['error' => __("This Email Id is already registered for this product.")];
                return $this->resultJson->create()->setData($result);
            } else {
                if ($this->_helper->getStockStatus($id)) {
                    
                    $children = $this->_helper->getChildIds($productCollection);
                    foreach ($children as $child) {
                        if (!$this->_helper->getStockStatus($child) || !$this->_helper->isSalable($child)) {
                            $productIds[$child] = $child;
                        }
                    }
                    $collection = $mCollection
                        ->addFieldToFilter("product_id", ['in' => $productIds])
                        ->addFieldToFilter("email", ['eq' => $email])
                        ->addFieldToFilter("status", ['eq' => 0]);
                }
            }
        } else {
            $collection = $model->getCollection()
                        ->addFieldToFilter("email", ['eq' => $email])
                        ->addFieldToFilter("product_id", ['eq' => $id])
                        ->addFieldToFilter("status", ['eq' => 0]);
                        
        }

        if ($collection->getSize() == 0) {
            if ($senderName == "" || $senderEmail == "") {
                $result = ['error' => __("Please ask admin to complete the details.")];
                return $this->resultJson->create()->setData($result);
            }

            if ($storeName == "" || $storeEmail == "") {
                $result = ['error' => __("Please ask admin to complete the details.")];
                return $this->resultJson->create()->setData($result);
            }

            $customer=$this->customerModel->create()
                            ->getCollection()
                            ->addFieldToFilter('email', ['eq'=>$email]);
            foreach ($customer as $customerInfo) {
                $customerName=$customerInfo->getFirstname()." ".$customerInfo->getLastname();
            }
            if (empty($customerName)) {
                $custName = "Buyer";
            } else {
                $custName = $customerName;
            }
            $receiverInfoCustomer = [
                'name' => $custName,
                'email' => $email
            ];
            $senderInfoCustomer = [
                'name' => $senderName,
                'email' => $senderEmail
            ];

            $emailTempVariables = [];
            $emailTempVariables['customerName'] = $custName;
            $emailTempVariables['adminName'] = $senderName;
            $emailTempVariables['productName'] = $productCollection->getName();
            $emailTempVariables['productUrl'] = $productCollection->getProductUrl();
            if ($notifyCustomer) {
                if ($senderName == "" || $senderEmail == "") {
                    $result = ['error' => __("Please ask admin to complete the details.")];
                    return $this->resultJson->create()->setData($result);
                }
                $this->_helper->productInStockRegistration(
                    $emailTempVariables,
                    $senderInfoCustomer,
                    $receiverInfoCustomer
                );
            }
            if ($notifyAdmin) {
                if ($storeName == "" || $storeEmail == "") {
                    $result = ['error' => __("Please ask admin to complete the details.")];
                    return $this->resultJson->create()->setData($result);
                }
                $receiverInfoAdmin = [
                    'name' => $senderName,
                    'email' => $senderEmail
                ];
                $senderInfoAdmin = [
                    'name' => $storeName,
                    'email' => $storeEmail
                ];

                $replyTo = [
                    'name' => $custName,
                    'email' => $email
                ];
                $emailTempVariables = [];
                $emailTempVariables['customerName'] = $custName;
                $emailTempVariables['storeName'] = $storeName;
                $emailTempVariables['productName'] = $productCollection->getName();
                $emailTempVariables['adminName'] = $senderName;
                $emailTempVariables['productUrl'] = $productCollection->getProductUrl();
                
                $this->_helper->adminNotification(
                    $emailTempVariables,
                    $senderInfoAdmin,
                    $receiverInfoAdmin,
                    $replyTo
                );
            }
            if ($productCollection->getTypeId()=='configurable') {
                if (!$this->_helper->getStockStatus($id)) {
                    $model->setEmail($email);
                    $model->setStatus('0');
                    $model->setProductId($id);
                    $model->setWebsiteCode($websiteCode);
                    $this->saveModel($model);
                } else {
                    foreach ($productIds as $productId) {
                        $model = $this->productFactory->create();
                        $model->setEmail($email);
                        $model->setStatus('0');
                        $model->setProductId($productId);
                        $model->setWebsiteCode($websiteCode);
                        $this->saveModel($model);
                    }
                }
            } else {
                $model->setEmail($email);
                $model->setStatus('0');
                $model->setProductId($id);
                $model->setWebsiteCode($websiteCode);
                $this->saveModel($model);
            }
            
            $result = ['msg' => __("Thank You! We will notify you when the product(s) will be in-stock!")];
            
            return $this->resultJson->create()->setData($result);
        } else {
            $result = ['error' => __("This Email Id is already registered for this product.")];
            return $this->resultJson->create()->setData($result);
        }
    }
}

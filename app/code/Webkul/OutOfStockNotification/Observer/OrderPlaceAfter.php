<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_ OutOfStockNotification
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\OutOfStockNotification\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Webkul\OutOfStockNotification\Logger\Logger;

/**
 * Webkul OutOfStocknotification Product Stock Status Observer Model.
 */
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * customer model
     * @var \Magento\Customer\Model\Customer
     */
    private $customerModel;

    /**
     * helper
     * @var \Webkul\OutOfStockNotification\Helper\Data
     */
    private $helper;

    /**
     * product catelog factory
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $proFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var GetSalableQuantityDataBySku
     */
    private $getSalableQuantityDataBySku;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\CustomerFactory $customerModel
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param \Webkul\OutOfStockNotification\Model\ProductFactory $proFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Webkul\OutOfStockNotification\Helper\Data $helper
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerFactory $customerModel,
        ProductRepositoryInterface $productRepositoryInterface,
        \Webkul\OutOfStockNotification\Model\ProductFactory $proFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Webkul\OutOfStockNotification\Helper\Data $helper,
        Logger $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerModel = $customerModel;
        $this->proFactory = $proFactory;
        $this->productRepository = $productRepositoryInterface;
        $this->resource = $resource;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Orderplaceafter observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $isLowStockNotificationAllowed = $this->scopeConfig->getValue(
                'notificationsettings/lowstock/allow',
                $storeScope
            );
            if ($isLowStockNotificationAllowed) {
                $lowStockQty = $this->scopeConfig->getValue(
                    'notificationsettings/lowstock/qty',
                    $storeScope
                );
                $lowStockReceivers = $this->scopeConfig->getValue(
                    'notificationsettings/lowstock/receivers',
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
                $helper = $this->helper;
                $order = $observer->getEvent()->getOrder();
                $order_id = $order->getIncrementId();
                
                foreach ($order->getAllItems() as $item) {
                    $productIds[]= $item->getProductId();
                }
                if (!empty($productIds)) {
                    $this->refractor($productIds, $lowStockQty, $lowStockReceivers);
                }
            }
        } catch (\Exception $e) {
            $this->logger->addError(' error in observer= OrderPlaceAfter '.$e->getMessage());
        }
    }

    /**
     * @param array
     * @return object
     */
    private function getCustomerCollection($subscribersEmailList)
    {
        $customerCollection = $this->customerModel->create()->getCollection();
        if (!empty($subscribersEmailList)) {
            $customerCollection->addFieldToFilter('email', ['nin'=>$subscribersEmailList]);
        }
        return $customerCollection;
    }

    /**
     * @param object
     * @param array
     */
    private function sendLowStockEmail($collection, $product)
    {
        if ($collection) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $senderName = $this->scopeConfig->getValue('notificationsettings/settings/adminname', $storeScope);
            $senderEmail = $this->scopeConfig->getValue('notificationsettings/settings/adminemail', $storeScope);
            foreach ($collection as $coll) {
                $customerName = "";
                $customerEmail = $coll->getEmail();
                $senderInfo = [];
                $receiverInfo = [];
                $customerName = $coll->getFirstname()." ".$coll->getLastName();
                if (empty($customerName) || $customerName == ' ') {
                    $custName = __("buyer");
                } else {
                    $custName = $customerName;
                }
                $receiverInfo = [
                'name' => $custName,
                'email' => $customerEmail
                 ];
                $senderInfo = [
                    'name' => $senderName,
                    'email' => $senderEmail
                 ];
                $emailTempVariables = [];
                $emailTempVariables['customerName'] = $custName;
                $emailTempVariables['productName'] = $product['name'];
                $emailTempVariables['stock'] = $product['qty'];
                $emailTempVariables['productUrl'] = $product['productUrl'];
                $emailTempVariables['adminName'] = $senderName;
                try {
                    $this->helper->lowStockEmail(
                        $emailTempVariables,
                        $senderInfo,
                        $receiverInfo
                    );
                } catch (\Exception $e) {
                    $this->logger->info("OutOfStockNotification OrderPlcaeAfter Observer =>".$e->getMessage());
                }
            }
        }
    }

    private function refractor($productIds, $lowStockQty, $lowStockReceivers)
    {
        $subscribersEmail = [];
        foreach ($productIds as $productId) {
            $productDetails = [];
            $product = $this->productRepository->getById($productId);
            // $salable = $this->getSalableQuantityDataBySku->execute($product->getSku());
            // if (!empty($salable[0]['qty'])) {
            //     $stockItemQty = $salable[0]['qty'];
            // } else {
                $stockItemQty = $product->getQty();
            //}
            $productDetails['qty'] = $stockItemQty;
            $productDetails['name'] = $product->getName();
            $productDetails['productUrl'] = $product->getProductUrl();
            if ($stockItemQty <= $lowStockQty && $stockItemQty >= 0) {
                $customerTable = $this->resource->getTableName('customer_entity');
                $oosnCollection = $this->proFactory->create()->getCollection()
                    ->addFieldToFilter('product_id', ['eq'=>$productId])
                    ->addFieldToSelect('email');
                $oosnCollection->getSelect()->joinleft(
                    $customerTable.' as ct',
                    'main_table.email = ct.email',
                    ['ct.firstname', 'ct.lastname']
                );
                // this will notify subscribers in both cases
                if ($oosnCollection->getSize() > 0) {
                    $this->sendLowStockEmail($oosnCollection, $productDetails);
                }
                // for notifying all the customers excluding subscribers.
                if ($lowStockReceivers > 0) {
                    if ($oosnCollection->getSize() > 0) {
                        foreach ($oosnCollection as $subscriber) {
                            $subscribersEmail[] = $subscriber->getEmail();
                        }
                    }
                    $customerCollection = $this->getCustomerCollection($subscribersEmail);
                    $this->sendLowStockEmail($customerCollection, $productDetails);
                }
            }
        }
    }
}

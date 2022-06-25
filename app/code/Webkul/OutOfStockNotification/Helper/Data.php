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
namespace Webkul\OutOfStockNotification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Products;

/**
 * OutOfStockNotification data helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_ADMIN_NOTIFICATION_MAIL = 'notificationsettings/emailsettings/admin_notification';
    const XML_PATH_PRODUCT_INSTOCK_REGISTRATION = 'notificationsettings/emailsettings/product_instock_registration';
    const XML_PATH_PRODUCT_INSTOCK_NOTIFICATION = 'notificationsettings/emailsettings/product_instock_notification';
    const XML_PATH_LOWSTOCK_EMAIL = 'notificationsettings/lowstock/low_stock_email';

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var use Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $configurable;

    /**
     * @var Products
     */
    private $productCollection;

    /**
     * @var  \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManager
     * @param \Magento\Framework\App\ResourceConnection           $resource
     * @param \Magento\Framework\Mail\Template\TransportBuilder   $transportBuilder
     * @param Products                                            $productCollection
     * @param \Magento\Catalog\Model\Product                      $product
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param \Magento\Framework\Translate\Inline\StateInterface  $inlineTranslation
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        Products $productCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->configurable = $configurable;
        $this->scopeConfig = $scopeConfig;
        $this->product = $productFactory;
        $this->productCollection = $productCollection;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Return store configuration value.
     *
     * @param string $path
     * @param int    $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return template id.
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Check Configurable Product is Preorder or Not.
     *
     * @param int $productId
     *
     * @return bool
     */
    public function isConfigOosn($productId, $stockStatus = '')
    {
        $isProduct = false;
        $collection = $this->productCollection->create();
        $collection->addFieldToFilter('entity_id', $productId);
        $collection->addAttributeToSelect('*');
        foreach ($collection as $item) {
            $product = $item;
            $isProduct = true;
            break;
        }
        if ($isProduct) {
            $productType = $product->getTypeId();
            if ($productType == 'configurable') {
                $configModel = $this->configurable;
                $usedProductIds = $configModel->getUsedProductIds($product);
                foreach ($usedProductIds as $usedProductId) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return store.
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * [product in stock notification description]
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     */
    public function productInstockNotification($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_PRODUCT_INSTOCK_NOTIFICATION);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * [product in stock notification description]
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     */
    public function lowStockEmail($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_LOWSTOCK_EMAIL);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * [product instock registration description]
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     */
    public function productInstockRegistration($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_PRODUCT_INSTOCK_REGISTRATION);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * [admin Notification]
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     */
    public function adminNotification($emailTemplateVariables, $senderInfo, $receiverInfo, $replyTo)
    {
        $this->_template = $this->getTemplateId(self::XML_PATH_ADMIN_NOTIFICATION_MAIL);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo, $replyTo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    /**
     * [generateTemplate description].
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     */
    public function generateTemplate(
        $emailTemplateVariables,
        $senderInfo,
        $receiverInfo,
        $replyTo = ['email' => '', 'name'=> '']
    ) {
    
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state =  $objectManager->get('Magento\Framework\App\State');  
        if ($state->getAreaCode() == 'frontend') {
                $template = $this->transportBuilder
                    ->setTemplateIdentifier($this->_template)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($senderInfo)
                    ->addTo($receiverInfo['email'], $receiverInfo['name']);
                    
            if ($replyTo['email'] != "") {
                $template->setReplyTo($replyTo['email'], $replyTo['name']);
            }

            return $this;
        } else {      
            $template = $this->transportBuilder
                    ->setTemplateIdentifier($this->_template)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($senderInfo)
                    ->addTo($receiverInfo['email']);
                    
            if ($replyTo['email'] != "") {
                $template->setReplyTo($replyTo['email'], $replyTo['name']);
            }

            return $this;
        }
    }

    /**
     *
     * @param  $productId
     * @return int
     */
    public function getStockStatus($productId)
    {
        $connection = $this->resource->getConnection();
        $stockDetails = false;
        $collection = $this->productCollection
            ->create()
            ->addAttributeToSelect('name');
        $table = $connection->getTableName('cataloginventory_stock_item');
        $bind = 'product_id = entity_id';
        $cond = '{{table}}.stock_id = 1';
        $type = 'left';
        $alias = 'is_in_stock';
        $field = 'is_in_stock';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $alias = 'qty';
        $field = 'qty';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $collection->addFieldToFilter('entity_id', $productId);
        foreach ($collection as $value) {
            $stockDetails = $value->getIsInStock();
        }
        return $stockDetails;
    }

    public function getFrontendProductUrl($productId)
    {
        $parent = $this->configurable->getParentIdsByChild($productId);
        if (isset($parent[0])) {
            return $this->storeManager->getStore()->getBaseUrl().'catalog/product/view/id/'.$parent[0];
        } else {
            return $this->storeManager->getStore()->getBaseUrl().'catalog/product/view/id/'.$productId;
        }
    }

    /**
     * getProduct function
     *
     * @param [int] $productId
     * @return object
     */
    public function getProduct($productId)
    {
        return $this->product->create()->load($productId)->isAvailable();
    }

    /**
     * @param product id
     * @return Bool
     */
    public function getConfigStockStatus($productId)
    {
        $statusFlag = 1;
        $result = [];
        $oosnAssociates = [];
        $configStockStatus = $this->getStockStatus($productId);
        if ($configStockStatus) {
            $product = $this->product->create()->load($productId);
            $childrenIds = $this->getChildIds($product);
            foreach ($childrenIds as $childrenId) {
                if ($this->getProduct($childrenId)) {
                     continue;
                } else {
                    $statusFlag = 0;
                    $oosnAssociates[$childrenId] = $this->getProductName($childrenId);
                }
            }
            $result['oosn_associates'] = $oosnAssociates;
            $result['stock_status'] = $statusFlag;
        } else {
            $result['oosn_associates'] = [];
            $result['stock_status'] = 0;
        }
        return $result;
    }

    /**
     * get product name
     *
     * @param int $productId
     * @return string
     */
    public function getProductName($productId)
    {
        return $this->product->create()->load($productId)->getName();
    }

    /**
     * get child ids
     *
     * @param Product $product
     * @return array
     */
    public function getChildIds($product)
    {
        $childrenIds = $product->getTypeInstance()->getChildrenIds($product->getId());
        $childrenIds = $childrenIds[0];
        return $childrenIds;
    }

    public function isSalable($productId)
    {
        return $this->product->create()->load($productId)->isSalable();
    }
}

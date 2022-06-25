<?php

namespace Meigee\Barbour\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Store\Model\Store;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class FrontHelper extends \Magento\Framework\Url\Helper\Data
{

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;
    protected $_request;
    protected $_registry;
    // protected $categoryFactory;

    public function __construct(
        // \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\Helper\Context $context,

        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
        $this->_request = $request;
        $this->_registry = $registry;
        // $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    public function isProductNew(ModelProduct $product)
    {
        $newsFromDate = $product->getNewsFromDate();
        $newsToDate = $product->getNewsToDate();
        if (!$newsFromDate && !$newsToDate) {
            return false;
        }

        return $this->localeDate->isScopeDateInInterval(
            $product->getStore(),
            $newsFromDate,
            $newsToDate
        );
    }
    public function isProductSale(ModelProduct $product)
    {
        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        if($regularPrice != $finalPrice){
            return true;
        } else {
            return false;
        }
    }

    public function isProductOnlyLeft(ModelProduct $product)
    {
        if($this->scopeConfig->getValue('barbour_general/barbour_labels/label_only_left', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $stockThreshold = $this->scopeConfig->getValue('cataloginventory/options/stock_threshold_qty', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productStockObj = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface');
            $productStockObj = $productStockObj->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
            if(!empty($productStockObj->getData())) {
                $productQty = $productStockObj->getQty();
                if($productQty != 0 and $productQty < $stockThreshold){
                    return '<span class="label-sale availability-only"> '.($productQty+1).' <strong>'.__('Left').'</strong></span>';
                }else{
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getProductSalePercent(ModelProduct $product)
    {
        if($this->scopeConfig->getValue('barbour_general/barbour_labels/label_sale_percentage', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
            $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            if($finalPrice && $regularPrice) {
                $sale = 100 - (($finalPrice * 100)/$regularPrice);
                $sale = number_format($sale);
                if($sale > 0) {
                    $sale = sprintf(__('<span class="label-sale percentage">%s <span>off</span></span>'), $sale.'%');
                } else {
                    $sale = '';
                }
            } else {
                $sale = '';
            }
            return $sale;
        } else {
            return false;
        }
    }

    public function getCookie($name)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cookieManager = $objectManager->create('Magento\Framework\Stdlib\CookieManagerInterface');
        $this->cookieManager = $cookieManager;
        return $this->cookieManager->getCookie($name);
    }

    public function getFbSidebar () {
        // $fboptions = $this->getThemeOptionsRound('meigee_round_sidebar');
        $height      = $this->scopeConfig->getValue('barbour_general/barbour_facebook_block/height', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $url         = $this->scopeConfig->getValue('barbour_general/barbour_facebook_block/url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $faces       = $this->scopeConfig->getValue('barbour_general/barbour_facebook_block/faces', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $small_header = $this->scopeConfig->getValue('barbour_general/barbour_facebook_block/smallheader', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $posts      = $this->scopeConfig->getValue('barbour_general/barbour_facebook_block/posts', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $cover      = $this->scopeConfig->getValue('barbour_general/barbour_facebook_block/cover', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $adaptive_width      = $this->scopeConfig->getValue('barbour_general/barbour_facebook_block/adaptive_width', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $fbcontent = array();
        $fbcontent[] = 'data-width="300"';
        $fbcontent[] = 'data-height="' . $height . '"';
        $fbcontent[] = 'data-href="' . $url . '"';
        $fbcontent[] = 'data-show-facepile="' . $faces . '"';
        $fbcontent[] = 'data-small-header="' . $small_header . '"';
        $fbcontent[] = 'data-adapt-container-width="' . $adaptive_width . '"';
        $fbcontent[] = 'data-hide-cover="' . $cover . '"';
        $fbcontent[] = 'data-show-posts="' . $posts . '"';
        return implode(' ',$fbcontent);
    }

    public function getCurrentPage()
    {
        return $this->_request->getFullActionName();
    }

    public function getCurrentCategory()
    {
        $category = $this->_registry->registry('current_category');
        return $category;
    }

    public function getCurrentProduct()
    {
        $product = $this->_registry->registry('current_product');
        return $product;
    }
	
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
				$config_path,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
				);
	}
    public function getFrontCookie($key)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
        return $cookieManager->getCookie($key);
    }
}

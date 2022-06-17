<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Seo\Helper;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;
use Mirasvit\Seo\Model\Config as Config;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectStoreFactory;

    protected $objectPagerFactory;

    protected $objectWrapperFilterFactory;

    protected $templateFactory;

    protected $categoryFactory;

    protected $productTypeFactory;

    protected $currencyFactory;

    protected $templateCollectionFactory;

    protected $categoryCollectionFactory;

    protected $productCollectionFactory;

    protected $seoParse;

    protected $coreString;

    protected $taxData;

    protected $context;

    protected $storeManager;

    protected $registry;

    protected $request;

    protected $seoDataFactory;

    protected $productTypeConfigurable;

    protected $productRepository;

    protected $product;

    protected $category;

    protected $stringPrepare;

    protected $parseObjects = [];

    protected $additional = [];

    protected $storeId = null;

    protected $titlePage = true;

    protected $descriptionPage = true;

    private $logo;

    private $config;

    private $objectManager;

    private $string;

    private $layerResolver;

    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Model\SeoObject\StoreFactory $objectStoreFactory,
        \Mirasvit\Seo\Model\SeoObject\PagerFactory $objectPagerFactory,
        \Mirasvit\Seo\Model\SeoObject\Wrapper\FilterFactory $objectWrapperFilterFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Bundle\Model\Product\TypeFactory $productTypeFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Mirasvit\Seo\Helper\Parse $seoParse,
        \Mirasvit\Core\Api\TextHelperInterface $coreString,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Model\SeoDataFactory $seoDataFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $productTypeConfigurable,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Mirasvit\Seo\Helper\StringPrepare $stringPrepare,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->config                     = $config;
        $this->objectStoreFactory         = $objectStoreFactory;
        $this->objectPagerFactory         = $objectPagerFactory;
        $this->objectWrapperFilterFactory = $objectWrapperFilterFactory;
        $this->categoryFactory            = $categoryFactory;
        $this->productTypeFactory         = $productTypeFactory;
        $this->currencyFactory            = $currencyFactory;
        $this->categoryCollectionFactory  = $categoryCollectionFactory;
        $this->productCollectionFactory   = $productCollectionFactory;
        $this->layerResolver              = $layerResolver;
        $this->seoParse                   = $seoParse;
        $this->coreString                 = $coreString;
        $this->taxData                    = $taxData;
        $this->context                    = $context;
        $this->storeManager               = $storeManager;
        $this->registry                   = $registry;
        $this->request                    = $context->getRequest();
        $this->seoDataFactory             = $seoDataFactory;
        $this->string                     = $string;
        $this->logo                       = $logo;
        $this->productTypeConfigurable    = $productTypeConfigurable;
        $this->productRepository          = $productRepository;
        $this->stringPrepare              = $stringPrepare;
        $this->objectManager              = $objectManager;
    }

    public function getBaseUri(): ?string
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return null;
        }

        $baseStoreUri = parse_url($this->context->getUrlBuilder()->getUrl(), PHP_URL_PATH);

        if ($baseStoreUri == '/') {
            return $_SERVER['REQUEST_URI'];
        } else {
            $requestUri = $_SERVER['REQUEST_URI'];
            $prepareUri = str_replace($baseStoreUri, '', $requestUri);

            if (substr($prepareUri, 0, 1) == '/') {
                return $prepareUri;
            } else {
                return '/' . $prepareUri;
            }
        }
    }

    protected function setAdditionalVariable(string $objectName, string $variableName, string $value): void
    {
        $this->additional[$objectName][$variableName] = $value;
        if (isset($this->parseObjects['product'])) {
            if ($objectName . '_' . $variableName == 'product_final_price_minimal') {
                $this->parseObjects['product']->setData('final_price_minimal', $value);
            }
            if ($objectName . '_' . $variableName == 'product_final_price_range') {
                $this->parseObjects['product']->setData('final_price_range', $value);
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _addParseObjects(ProductInterface $product = null): void
    {
        if ($this->parseObjects && $this->storeId !== null && !$product) {
            return;
        }

        $this->product = ($product) ? : $this->registry->registry('current_product');
        if (!$this->product) {
            $this->product = $this->registry->registry('product');
        }
        if ($this->product) {
            $this->parseObjects['product'] = $this->product;
            $this->setAdditionalVariable('product', 'final_price', $this->product->getFinalPrice());
            $this->setAdditionalVariable('product', 'url', $this->product->getProductUrl());
            $this->setAdditionalVariable(
                'product',
                'final_price_minimal',
                $this->getCurrentProductFinalPrice($this->product)
            );
            $this->setAdditionalVariable(
                'product',
                'final_price_range',
                $this->getCurrentProductFinalPriceRange($this->product)
            );
        }

        $this->category = $this->registry->registry('current_category');

        $this->parseObjects['store']  = $this->objectStoreFactory->create();
        $this->parseObjects['pager']  = $this->objectPagerFactory->create();
        $this->parseObjects['filter'] = $this->objectWrapperFilterFactory->create();

        if ($this->category) {
            $this->parseObjects['category'] = $this->category;
            if ($brand = $this->registry->registry('current_brand')) {
                $this->setAdditionalVariable('category', 'brand_name', $brand->getValue());
            }
            if ($this->category && $parent = $this->category->getParentCategory()) {
                $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
                if ($parent->getId() > $rootCategoryId) {
                    if (($parentParent = $parent->getParentCategory())
                        && ($parentParent->getId() > $rootCategoryId)
                    ) {
                        $this->setAdditionalVariable('category', 'parent_parent_name', $parentParent->getName());
                    }
                    $this->setAdditionalVariable('category', 'parent_name', $parent->getName());
                    $this->setAdditionalVariable('category', 'parent_url', $parent->getUrl());
                }
                $this->setAdditionalVariable('category', 'url', $this->category->getUrl());
                //alias to meta_title
                $this->setAdditionalVariable('category', 'page_title', $this->category->getMetaTitle());
            }
        }

        //Mageplaza Shopbybrand brand value [store_mp_brand]
        if ($this->getFullActionCode() == 'mpbrand_index_view'
            && ($manufacturerOptionId = $this->request->getParam('manufacturer'))
            && class_exists(\Mageplaza\Shopbybrand\Model\BrandFactory::class)) {
            $manufacturerValue = $this->objectManager->get(\Mageplaza\Shopbybrand\Model\BrandFactory::class)
                ->create()->loadByOption($manufacturerOptionId)->getValue();
            if ($manufacturerValue) {
                $this->setAdditionalVariable('store', 'mp_brand', $manufacturerValue);
            }
        }

        $this->storeId = $this->storeManager->getStore();

        return;
    }

    public function getCurrentSeoShortDescriptionForSearch(ProductInterface $product): ?string
    {
        if ($this->storeManager->getStore()->getCode() == 'admin') {
            return null;
        }

        $categoryIds    = $product->getCategoryIds();
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        array_unshift($categoryIds, $rootCategoryId);
        $categoryIds         = array_reverse($categoryIds);
        $storeId             = $this->storeManager->getStore()->getStoreId();
        $seoShortDescription = null;
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryFactory->create()->setStoreId($storeId)->load($categoryId);
            if ($seoShortDescription = $category->getProductShortDescriptionTpl()) {
                break;
            }
        }

        if ($seoShortDescription) {
            $this->parseObjects['product'] = $product;
            $seoShortDescription           = $this->seoParse->parse(
                $seoShortDescription,
                $this->parseObjects,
                $this->additional,
                $storeId
            );
        }

        return $seoShortDescription;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function checkPattern(string $string, string $pattern, bool $caseSensative = false): bool
    {
        if (!$caseSensative) {
            $string  = strtolower($string);
            $pattern = strtolower($pattern);
        }

        $parts = explode('*', $pattern);
        $index = 0;

        $shouldBeFirst = true;

        foreach ($parts as $part) {
            if ($part == '') {
                $shouldBeFirst = false;
                continue;
            }

            $index = strpos($string, $part, $index);

            if ($index === false) {
                return false;
            }

            if ($shouldBeFirst && $index > 0) {
                return false;
            }

            $shouldBeFirst = false;
            $index         += strlen($part);
        }

        if (count($parts) == 1) {
            return $string == $pattern;
        }

        $last = end($parts);
        if ($last == '') {
            return true;
        }

        if (strrpos($string, $last) === false) {
            return false;
        }

        if (strlen($string) - strlen($last) - strrpos($string, $last) > 0) {
            return false;
        }

        return true;
    }

    public function cleanMetaTag(string $tag): string
    {
        $tag = strip_tags($tag);
        $tag = preg_replace('/\s{2,}/', ' ', $tag); //remove unnecessary spaces
        $tag = preg_replace('/\"/', ' ', $tag); //remove " because it destroys html
        $tag = trim($tag);

        return $tag;
    }

    public function getMetaRobotsByCode(int $code = null): ?string
    {
        switch ($code) {
            case Config::NOINDEX_NOFOLLOW:
                return 'NOINDEX,NOFOLLOW';
            case Config::NOINDEX_FOLLOW:
                return 'NOINDEX,FOLLOW';
            case Config::INDEX_NOFOLLOW:
                return 'INDEX,NOFOLLOW';
            default:
                return null;
        };
    }

    public function getProductSeoCategory(ProductInterface $product): CategoryInterface
    {
        $categoryId = $product->getSeoCategory();
        $category   = $this->registry->registry('current_category');

        if ($category && !$categoryId) {
            return $category;
        }

        if (!$categoryId) {
            $categoryIds = $product->getCategoryIds();
            if (count($categoryIds) > 0) {
                //we need this for multi websites configuration
                $categoryRootId = $this->storeManager->getStore()->getRootCategoryId();
                $category       = $this->categoryCollectionFactory->create()
                    ->addFieldToFilter('path', ['like' => "%/{$categoryRootId}/%"])
                    ->addFieldToFilter('entity_id', $categoryIds)
                    ->setOrder('level', 'desc')
                    ->setOrder('entity_id', 'desc')
                    ->getFirstItem();
                $categoryId     = $category->getId();
            }
        }
        //load category with flat data attributes
        $category = $this->categoryFactory->create()->load($categoryId);

        return $category;
    }

    public function getInactiveCategories(): array
    {
        $inactiveCategories = $this->categoryFactory->create()
            ->getCollection()
            ->setStoreId($this->storeManager->getStore()->getId())
            ->addFieldToFilter('is_active', ['neq' => '1'])
            ->addAttributeToSelect('*');
        $inactiveCat        = [];
        foreach ($inactiveCategories as $inactiveCategory) {
            $inactiveCat[] = $inactiveCategory->getId();
        }

        return $inactiveCat;
    }

    public function getFullActionCode(): string
    {
        $result = strtolower($this->request->getModuleName() . '_' . $this->request->getControllerName()
            . '_' . $this->request->getActionName());

        return $result;
    }

    public function isOnLandingPage(): bool
    {
        return (bool)$this->request->getParam('am_landing');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getCurrentProductFinalPrice(ProductInterface $product, bool $noSymbol = false): ?float
    {
        $productFinalPrice = false;
        $currencyCode      = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $priceModel        = $product->getPriceModel();

        if ($product->getTypeId() == 'grouped') {
            $finalPrice = $this->_getGroupedMinimalPrice($product);
        } else {
            //var1, works not for every store
            //$finalPrice = $priceModel->getFinalPrice(null, $product);
            //var2 - final price with tax
            $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        }

        if ($finalPrice && ($finalPrice = $this->_formatPrice($finalPrice, $noSymbol)) && $currencyCode) {
            $productFinalPrice = $finalPrice;
        }

        if ($productFinalPrice) {
            return $productFinalPrice;
        }

        return null;
    }

    public function getCurrentProductFinalPriceRange(ProductInterface $product): ?string
    {
        $productFinalPrice = false;
        $currencyCode      = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $priceModel        = $product->getPriceModel();

        if ($product->getTypeId() == 'grouped') {
            $finalPrice = $this->_getGroupedPriceRange($product);
        } elseif ($product->getTypeId() == 'configurable') {
            $finalPrice = $this->_getConfigurablePriceRange($product);
        } else {
            $finalPrice = $priceModel->getFinalPrice(null, $product);
            $finalPrice = $this->_formatPrice($finalPrice, false);
        }

        if ($finalPrice && $currencyCode) {
            $productFinalPrice = $finalPrice;
        }

        if ($productFinalPrice) {
            return $productFinalPrice;
        }

        return null;
    }

    /**
     * @param string    $price
     * @param bool|true $noSymbol
     * @return bool|float
     */
    protected function _formatPrice(string $price, bool $noSymbol = true): ?bool
    {
        $displaySymbol = $noSymbol ? [
            'display' => \Zend_Currency::NO_SYMBOL,
        ] : ['display' => \Zend_Currency::USE_SYMBOL];
        if (intval($price)) {
            $price = $this->currencyFactory->create()->format(
                (float)$price,
                $displaySymbol,
                false
            );

            return $price;
        }

        return null;
    }

    protected function _getGroupedMinimalPrice(ProductInterface $product): float
    {
        $product = $this->productCollectionFactory->create()
            ->addMinimalPrice()
            ->addFieldToFilter('entity_id', $product->getId())
            ->getFirstItem();

        return (float)$product->getMinimalPrice();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     */
    protected function _getGroupedPriceRange(ProductInterface $product): ?string
    {
        $groupedPrices      = [];
        $typeInstance       = $product->getTypeInstance();
        $associatedProducts = $typeInstance->setStoreFilter($product->getStore(), $product)
            ->getAssociatedProducts($product);

        foreach ($associatedProducts as $childProduct) {
            if ($childProduct->isAvailable() && ($childProduct->isSaleable() || $childProduct->getIsInStock() > 0)) {
                $groupedPrices[] = $childProduct->getFinalPrice(1);
            }
        }
        if (count($groupedPrices)
            && ($minGroupedPrice = min($groupedPrices))
            && ($maxGroupedPrice = max($groupedPrices))
            && $minGroupedPrice != $maxGroupedPrice
        ) {
            $groupedPriceRange = $this->_formatPrice($minGroupedPrice, false)
                . ' - ' . $this->_formatPrice($maxGroupedPrice, false);
        } else {
            $groupedPriceRange = $this->_getGroupedMinimalPrice($product);
        }

        return $groupedPriceRange;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getConfigurablePriceRange(ProductInterface $product): ?string
    {
        $price           = [];
        $childProductIds = $this->productTypeConfigurable->getChildrenIds($product->getId());

        if (isset($childProductIds[0])) {
            foreach ($childProductIds[0] as $childProductId) {
                $childProduct = $this->productRepository->getById($childProductId);
                $priceModel   = $childProduct->getPriceModel();
                $price []     = $priceModel->getFinalPrice(null, $childProduct);
            }
        }

        if (count($price)
            && ($minPrice = min($price))
            && ($maxPrice = max($price))
            && $minPrice != $maxPrice
        ) {
            $priceRange = $this->_formatPrice($minPrice, false)
                . ' - ' . $this->_formatPrice($maxPrice, false);
        } elseif (count($price) && ($minPrice = min($price))) {
            $priceRange = $this->_formatPrice($minPrice, false);
        }

        if (!isset($priceRange)) {
            $priceRange = null;
        }

        return $priceRange;
    }

    public function getLogoUrl(): string
    {
        return (string)$this->logo->getLogoSrc();
    }

    public function getLogoAlt(): string
    {
        return (string)$this->logo->getLogoAlt();
    }

    /**
     * Ignored actions for controller_action_postdispatch and controller_front_send_response_before.
     */
    public function isIgnoredActions(): bool
    {
        //@todo add all account pages
        $ignoredActions = ['review_product_listajax', 'customer_address_form', 'customer_address_index', 'returns_attachment_download'];
        if (in_array($this->getFullActionCode(), $ignoredActions)) {
            return true;
        }

        if (strpos($this->getFullActionCode(), 'paypal_express') !== false) {
            return true;
        }

        if ($this->request->isAjax()) {
            return true;
        }

        return false;
    }

    /**
     * Cancel ignored actions (other extension use plugin for isIgnoredActions function)
     */
    public function cancelIgnoredActions(): bool
    {
        $cancelIgnoredActions = [AlternateConfig::AMASTY_XLANDING];
        if (in_array($this->getFullActionCode(), $cancelIgnoredActions)) {
            return true;
        }

        return false;
    }

    /**
     * Ignored urls.
     */
    public function isIgnoredUrls(): bool
    {
        $ignoredUrlParts = ['checkout/', 'onestepcheckout'];
        $currentUrl      = $this->context->getUrlBuilder()->getCurrentUrl();
        foreach ($ignoredUrlParts as $urlPart) {
            if (strpos($currentUrl, $urlPart) !== false) {
                return true;
            }
        }
        if ($this->getFullActionCode() == 'checkout_cart_index'
            || $this->getFullActionCode() == 'checkout_onepage_index') {
            return true;
        }

        return false;
    }
}

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



namespace Mirasvit\SeoMarkup\Block\Rs;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;

class Brand extends Template
{
    const BRAND_DEFAULT_NAME = 'BrandDefaultName';
    const BRAND_DATA         = 'm__BrandData';

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;
    
    /**
     * @var CategoryConfig
     */
    private $categoryConfig;

    /**
     * @var TemplateEngineServiceInterface
     */
    private $templateEngineService;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Brand constructor.
     * @param CategoryConfig $categoryConfig
     * @param TemplateEngineServiceInterface $templateEngineService
     * @param LayerResolver $layerResolver
     * @param Registry $registry
     * @param ModuleManager $moduleManager
     * @param ObjectManagerInterface $objectManager
     * @param Context $context
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        CategoryConfig $categoryConfig,
        TemplateEngineServiceInterface $templateEngineService,
        LayerResolver $layerResolver,
        Registry $registry,
        ModuleManager $moduleManager,
        ObjectManagerInterface $objectManager,
        Context $context
    ) {
        $this->categoryConfig        = $categoryConfig;
        $this->templateEngineService = $templateEngineService;
        $this->layerResolver         = $layerResolver;
        $this->store                 = $context->getStoreManager()->getStore();
        $this->registry              = $registry;
        $this->moduleManager         = $moduleManager;
        $this->objectManager         = $objectManager;

        parent::__construct($context);
    }

    /**
     * @return bool|string
     */
    protected function _toHtml()
    {
        $data = $this->getJsonData();

        if (!$data) {
            return false;
        }

        return '<script type="application/ld+json">' . \Zend_Json::encode($data) . '</script>';
    }


    /**
     * @return bool|array
     */
    public function getJsonData()
    {
        $brandTitle = $this->getBrandTitle();

        if (!$this->moduleManager->isEnabled('Mirasvit_Brand') || empty($brandTitle)) {
            return null;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->layerResolver->get()->getProductCollection();
        if (strripos($collection->getSelect()->__toString(), 'limit') === false) {
            $pageSize = $this->categoryConfig->getDefaultPageSize($this->store);
            $pageNum  = 1;

            if ($toolbar = $this->getLayout()->getBlock('product_list_toolbar')) {
                $pageSize = $toolbar->getLimit();
            }

            if ($pager = $this->getLayout()->getBlock('product_list_toolbar_pager')) {
                $pageNum = $pager->getCurrentPage();
            }

            $collection->setPageSize($pageSize)->setCurPage($pageNum);
        }

        if (!$collection || !$collection->getSize()) {
            return null;
        }

        return [
            '@context'   => 'http://schema.org',
            '@type'      => 'WebPage',
            'url'        => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
            'mainEntity' => [
                '@context'        => 'http://schema.org',
                '@type'           => 'OfferCatalog',
                'name'            => $brandTitle,
                'url'             => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
                'numberOfItems'   => $collection->getSize(),
                'itemListElement' => $this->getItemList($collection),
            ],
        ];
    }

    /**
     * @param mixed $collection
     * @return array
     */
    private function getItemList($collection)
    {
        $data = [];

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $item = [
                '@type' => 'Product',
                'name'  => $product->getName(),
                'url'   => $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]),
            ];

            if ($this->categoryConfig->getProductOffersType($this->store)) {
                $offer = $this->getProductOffer($product);

                if ($offer) {
                    $item['offers'] = $offer;
                }
            }

            $data[] = $item;
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array|false
     */
    private function getProductOffer($product)
    {
        $price = $product->getFinalPrice();

        if (!$price) {
            return false;
        }

        $productAvailability = method_exists($product, 'isAvailable')
            ? $product->isAvailable()
            : $product->isInStock();

        if ($productAvailability) {
            $condition = "http://schema.org/InStock";
        } else {
            $condition = "http://schema.org/OutOfStock";
        }

        return [
            '@type'         => 'http://schema.org/Offer',
            'price'         => number_format($price, 2),
            'priceCurrency' => $this->store->getCurrentCurrencyCode(),
            'availability'  => $condition,
        ];
    }

    /**
     * @return string|false
     */
    private function getBrandTitle() {
        $brandRegistry = $this->objectManager->get('Mirasvit\Brand\Registry');

        return $brandRegistry->getBrandPage()->getBrandTitle();
    }
}

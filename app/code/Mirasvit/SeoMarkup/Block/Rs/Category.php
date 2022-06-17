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

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;
use Mirasvit\SeoMarkup\Service\ProductRichSnippetsService;

class Category extends Template
{
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $category;

    /**
     * @var CategoryConfig
     */
    private $categoryConfig;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var TemplateEngineServiceInterface
     */
    private $templateEngineService;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductRichSnippetsService
     */
    private $productSnippetService;

    /**
     * Category constructor.
     * @param CategoryConfig $categoryConfig
     * @param ProductCollectionFactory $productCollectionFactory
     * @param TemplateEngineServiceInterface $templateEngineService
     * @param Registry $registry
     * @param Context $context
     * @param ProductRichSnippetsService $productSnippetService
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        CategoryConfig $categoryConfig,
        ProductCollectionFactory $productCollectionFactory,
        TemplateEngineServiceInterface $templateEngineService,
        Registry $registry,
        Context $context,
        ProductRichSnippetsService $productSnippetService
    ) {
        $this->categoryConfig           = $categoryConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->templateEngineService    = $templateEngineService;
        $this->store                    = $context->getStoreManager()->getStore();
        $this->registry                 = $registry;
        $this->productSnippetService    = $productSnippetService;

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
        $this->category = $this->registry->registry('current_category');

        if (!$this->category) {
            return false;
        }

        if ($this->category->getId() == $this->store->getRootCategoryId()) {
            return false;
        }

        if (!$this->categoryConfig->isRsEnabled($this->store)) {
            return false;
        }

        $result[] = $this->getDataAsWebPage();

        return $result;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDataAsWebPage()
    {
        $collection = $this->getCollection();
        $itemList   = [];

        if ($collection) {
            $itemList = $this->getItemList($collection);
        }

        $result = [
            '@context'   => 'http://schema.org',
            '@type'      => 'WebPage',
            'url'        => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
            'mainEntity' => [
                '@type'           => 'offerCatalog',
                'name'            => $this->category->getName(),
                'url'             => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
                'numberOfItems'   => $collection ? $collection->count() : '',
                'itemListElement' => $itemList,
            ],
        ];

        return $result;
    }

    /**
     * @return bool|\Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCollection()
    {
        $productOffersType = $this->categoryConfig->getProductOffersType($this->store);
        switch ($productOffersType) {
            case (CategoryConfig::PRODUCT_OFFERS_TYPE_DISABLED):
                return false;
                break;

            case (CategoryConfig::PRODUCT_OFFERS_TYPE_CURRENT_PAGE):
                $categoryProductsListBlock = $this->getLayout()->getBlock('category.products.list');

                if ($categoryProductsListBlock) {
                    $collection = $categoryProductsListBlock->getLoadedProductCollection();

                    $ids = [];

                    foreach ($collection as $product) {
                        $ids[] = $product->getId();
                    }

                    $collection = $this->productCollectionFactory->create();
                    $collection->addAttributeToSelect('*');
                    $collection->addAttributeToFilter(
                        'entity_id',
                        ['in' => $ids]
                    );
                    $collection->addFinalPrice();
                    $collection->load();
                } else {
                    return;
                }
                break;

            case (CategoryConfig::PRODUCT_OFFERS_TYPE_CURRENT_CATEGORY):
                $collection = $this->productCollectionFactory->create();
                $collection->addAttributeToSelect('*');
                $collection->addCategoryFilter($this->category);
                $collection->addAttributeToFilter(
                    'visibility',
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
                );
                $collection->addAttributeToFilter(
                    'status',
                    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
                );
                $collection->addFinalPrice();
                $collection->load();
                break;
        }

        return $collection;
    }

    /**
     * @param mixed $collection
     * @return array
     */
    private function getItemList($collection)
    {
        $data = [];

        foreach ($collection as $product) {
            $data[] = $this->productSnippetService->getJsonData($product, true);
        }

        return $data;
    }
}

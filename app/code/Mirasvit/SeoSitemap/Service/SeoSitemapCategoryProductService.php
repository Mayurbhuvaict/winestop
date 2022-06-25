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



namespace Mirasvit\SeoSitemap\Service;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Mirasvit\SeoSitemap\Helper\Data;
use Mirasvit\SeoSitemap\Model\Config;
use Mirasvit\SeoSitemap\Model\Config\LinkSitemapConfig;

class SeoSitemapCategoryProductService
{
    /**
     * @var \Mirasvit\SeoSitemap\Model\Config\LinkSitemapConfig
     */
    private $linkSitemapConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Mirasvit\SeoSitemap\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\SeoSitemap\Helper\Data
     */
    protected $seoSitemapData;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $store;

    /**
     * @var array
     */
    private $categoriesTree;
    /**
     * @var array
     */
    private $excludeLinks;

    /**
     * SeoSitemapCategoryProductService constructor.
     * @param LinkSitemapConfig $linkSitemapConfig
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Config $config
     * @param Data $seoSitemapData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        LinkSitemapConfig $linkSitemapConfig,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Config $config,
        Data $seoSitemapData,
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->linkSitemapConfig         = $linkSitemapConfig;
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->config                    = $config;
        $this->seoSitemapData            = $seoSitemapData;
        $this->store                     = $context->getStoreManager()->getStore();
    }

    /**
     * @return array
     */
    private function getExcludeLinks()
    {
        if (empty($this->excludeLinks)) {
            $this->excludeLinks = $this->linkSitemapConfig->getExcludeLinks($this->store);
        }

        return $this->excludeLinks;
    }

    /**
     * @return array
     */
    public function getCategoryProductsTree()
    {
        return $this->getCategoriesTree();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStoreCategories()
    {
        $categories = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['neq' => \Magento\Catalog\Model\Category::TREE_ROOT_ID])
            ->addAttributeToFilter('entity_id', ['neq' => $this->store->getRootCategoryId()])
            ->addAttributeToFilter('parent_id', ['eq' => $this->store->getRootCategoryId()])
            ->setOrder('position')
            ->setStore($this->store);

        return $categories;
    }

    /**
     * @param int $level
     * @return array
     */
    private function getCategoriesTree($level = 0)
    {
        if ($this->categoriesTree) {
            return $this->categoriesTree;
        }

        $activeCategories = $this->getStoreCategories();

        if (!$activeCategories->getSize() > 0) {
            return [];
        }

        foreach ($activeCategories as $category) {
            if ($this->seoSitemapData->checkArrayPattern($category->getUrl(), $this->getExcludeLinks())) {
                $this->categoriesTree[$category->getId() . '-hidden'] = $category;
            } else {
                $category->setLevel($level);
                $this->categoriesTree[] = $category;
            }
            $this->_getCategoriesTree($category, $level + 1);
        }

        $this->prepareCategoryTree();

        return $this->categoriesTree;
    }

    /**
     * @param mixed $category
     * @param mixed $level
     */
    private function _getCategoriesTree($category, $level)
    {
        $children = $category->load($category->getId())->getChildrenCategories();

        foreach ($children as $child) {
            if (!$child->getIsActive()) {
                continue;
            }

            if ($this->seoSitemapData->checkArrayPattern($child->getUrl(), $this->getExcludeLinks())) {
                $this->categoriesTree[$child->getId() . '-hidden'] = $child;
            } else {
                $child->setLevel($level);
                $this->categoriesTree[] = $child;
            }
            $this->_getCategoriesTree($child, $level + 1);
        }
    }

    protected function prepareCategoryTree()
    {
        $result = [];
        foreach ($this->categoriesTree as $key => $category) {
            if (is_numeric($key)) {
                $objCategory = new \Magento\Framework\DataObject();
                $objCategory->setLevel($category->getLevel());
                $objCategory->setUrl($category->getUrl());
                $objCategory->setName($category->getName());
                $result[] = $objCategory;

                if ($this->config->getIsShowProducts()) {
                    $products = $this->getProductCollection($category);
                    foreach ($products as $product) {
                        if ($product->getVisibility() != 1 &&
                            !$this->seoSitemapData->checkArrayPattern($product->getProductUrl(), $this->getExcludeLinks())) {
                            $objProduct = new \Magento\Framework\DataObject();
                            $objProduct->setLevel($category->getLevel() + 1);
                            $objProduct->setUrl($product->getProductUrl());
                            $objProduct->setName($product->getName());
                            $result[] = $objProduct;
                        }
                        unset($product);
                        unset($objCategory);
                        unset($objProduct);
                    }
                } else {
                    continue;
                }
            }
            unset($category);
            unset($products);
        }

        $this->categoriesTree = $result;
    }

    /**
     * @param mixed $category
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection($category)
    {
        $collection = $this->productCollectionFactory->create()
            ->addStoreFilter($this->store)
            ->addCategoryFilter($category)
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToSelect('*')
            ->addUrlRewrite();

        return $collection;
    }
}

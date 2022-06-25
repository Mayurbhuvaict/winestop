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

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Data\Collection;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\BreadcrumbListConfig;

class BreadcrumbList extends Template
{
    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $store;

    /**
     * @var BreadcrumbListConfig
     */
    private $breadcrumbListConfig;

    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * @var StateServiceInterface
     */
    private $stateService;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * BreadcrumbList constructor.
     * @param BreadcrumbListConfig $breadcrumbListConfig
     * @param CatalogHelper $catalogHelper
     * @param StateServiceInterface $stateService
     * @param Registry $registry
     * @param Context $context
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        BreadcrumbListConfig $breadcrumbListConfig,
        CatalogHelper $catalogHelper,
        StateServiceInterface $stateService,
        Registry $registry,
        Context $context
    ) {
        $this->breadcrumbListConfig = $breadcrumbListConfig;
        $this->catalogHelper        = $catalogHelper;
        $this->stateService         = $stateService;
        $this->store                = $context->getStoreManager()->getStore();
        $this->registry             = $registry;

        parent::__construct($context);
    }

    /**
     * @return bool|string
     */
    protected function _toHtml()
    {
        if (!$this->breadcrumbListConfig->isRsEnabled($this->store)) {
            return false;
        }

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
        $crumbs = $this->registry->registry(BreadcrumbListConfig::REGISTER_KEY);

        if ($this->stateService->isProductPage()) {
            $path = $this->getProductBreadcrumbPath($this->stateService->getProduct());

            $crumbs = [];
            foreach ($path as $item) {
                $url = isset($item['link']) ? $item['link'] : $this->_urlBuilder->getCurrentUrl();

                $crumbs[$url] = $item['label'];
            }
        }

        if (!$crumbs || count($crumbs) === 0) {
            return null;
        }

        $data = [
            '@context'        => 'http://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $i = 1;
        foreach ($crumbs as $url => $label) {
            $data['itemListElement'][] = [
                '@type'    => "ListItem",
                'position' => $i,
                'item'     => [
                    '@id'  => $url,
                    'name' => strip_tags(trim($label)),
                ],
            ];

            $i++;
        }

        return $data;
    }

    /**
     * Returns current breadcrumb (if present) or Deepest category breadcrumb
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    private function getProductBreadcrumbPath($product)
    {
        $path = $this->catalogHelper->getBreadcrumbPath();

        if (count($path) > 1) {
            return $path;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $product->getCategoryCollection();

        $collection
            ->addAttributeToSelect('is_active')
            ->addAttributeToSelect('name')
            ->setOrder('level', Collection::SORT_ORDER_DESC);

        $pool           = [];
        $targetCategory = null;

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($collection as $category) {
            $pool[$category->getId()] = $category;

            if (!$category->getIsActive()) {
                continue;
            }

            // all parent categories must be active
            $child = $category;
            try {
                while ($child->getLevel() > 1 && $parent = $child->getParentCategory()) {
                    $pool[$parent->getId()] = $parent;

                    if (!$parent->getIsActive()) {
                        $category = null;
                        break;
                    }

                    $child = $parent;
                }
            } catch (\Exception $e) {
                // Not found exception is possible (corrupted data in DB)
                $category = null;
            }

            if ($category) {
                $targetCategory = $category;

                break;
            }
        }

        $path = [];

        if ($targetCategory) {
            $pathInStore = $category->getPathInStore();
            $pathIds     = array_reverse(explode(',', $pathInStore));

            foreach ($pathIds as $categoryId) {
                if (isset($pool[$categoryId]) && $pool[$categoryId]->getName()) {
                    $category = $pool[$categoryId];

                    $path[] = [
                        'label' => $category->getName(),
                        'link'  => $category->getUrl(),
                    ];
                }
            }
        }

        $path[] = ['label' => $product->getName()];

        return $path;
    }
}

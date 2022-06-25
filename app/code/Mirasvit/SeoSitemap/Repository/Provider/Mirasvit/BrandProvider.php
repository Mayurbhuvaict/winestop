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



namespace Mirasvit\SeoSitemap\Repository\Provider\Mirasvit;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sitemap\Helper\Data as DataHelper;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class BrandProvider implements ProviderInterface
{
    private $dataHelper;

    private $objectManager;

    private $scopeConfig;

    public function __construct(
        ObjectManagerInterface $objectManager,
        DataHelper $sitemapData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->dataHelper    = $sitemapData;
        $this->scopeConfig   = $scopeConfig;
    }

    public function getModuleName()
    {
        return 'Mirasvit_Brand';
    }

    public function isApplicable()
    {
        return true;
    }

    public function getTitle()
    {
        return __('Brand');
    }

    public function initSitemapItem($storeId)
    {
        $result = [];

        $result[] = new DataObject([
            'changefreq' => $this->dataHelper->getPageChangefreq($storeId),
            'priority'   => $this->dataHelper->getPagePriority($storeId),
            'collection' => $this->getItems($storeId),
        ]);

        return $result;
    }

    public function getItems($storeId)
    {
        $collection = $this->objectManager->create('Mirasvit\Brand\Model\ResourceModel\BrandPage\Collection');
        $brandUrlService = $this->objectManager->create('Mirasvit\Brand\Service\BrandUrlService');
        $collection->addStoreFilter($storeId);
        $collection->addEnableFilter();

        $items = [];

        foreach ($collection as $key => $brand) {
            $items[] = new DataObject([
                'id'         => $brand->getBrandPageId(),
                'url'        => $brandUrlService->getBrandUrl($brand, $storeId),
                'title'      => $brand->getBrandTitle(),
            ]);
        }
        return $items;
    }
}

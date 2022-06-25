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



namespace Mirasvit\SeoSitemap\Block;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;
use Mirasvit\SeoSitemap\Block\Map\Product as ProductBlock;
use Mirasvit\SeoSitemap\Helper\Data as SeoSitemapData;
use Mirasvit\SeoSitemap\Model\Config;
use Mirasvit\SeoSitemap\Model\Pager\Collection as PagerCollection;
use Mirasvit\SeoSitemap\Repository\ProviderRepository;
use Mirasvit\SeoSitemap\Service\SeoSitemapCategoryProductService;

/**
 * Class Map
 * @package Mirasvit\SeoSitemap\Block
 */
class Map extends Template
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var SeoSitemapCategoryProductService
     */
    private $categoryProductService;

    /**
     * @var PagerCollection
     */
    private $pagerCollection;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var ProductBlock
     */
    private $productBlock;

    /**
     * @var SeoSitemapData
     */
    private $seoSitemapData;

    /**
     * Map constructor.
     *
     * @param ProviderRepository               $providerRepository
     * @param SeoSitemapData                   $seoSitemapData
     * @param SeoSitemapCategoryProductService $categoryProductService
     * @param PagerCollection                  $pagerCollection
     * @param Config                           $config
     * @param Context                          $context
     * @param ProductBlock                     $productBlock
     */
    public function __construct(
        ProviderRepository $providerRepository,
        SeoSitemapData $seoSitemapData,
        SeoSitemapCategoryProductService $categoryProductService,
        PagerCollection $pagerCollection,
        Config $config,
        Context $context,
        ProductBlock $productBlock
    ) {
        $this->providerRepository     = $providerRepository;
        $this->categoryProductService = $categoryProductService;
        $this->pagerCollection        = $pagerCollection;
        $this->config                 = $config;
        $this->context                = $context;
        $this->productBlock           = $productBlock;
        $this->pageConfig             = $context->getPageConfig();
        $this->seoSitemapData         = $seoSitemapData;

        parent::__construct($context, []);
    }

    /**
     * @return array
     */
    public function getSplitByLetterCollection()
    {
        $splittedList = [];

        foreach ($this->getProductCollection() as $item) {
            if ($this->seoSitemapData->checkIsUrlExcluded($item->getProductUrl())) {
                continue;
            }

            $firstLetter                  = mb_substr($item->getName(), 0, 1);
            $firstLetter                  = mb_strtoupper($firstLetter);
            $splittedList[$firstLetter][] = [
                'name' => $item->getName(),
                'url'  => $item->getProductUrl(),
            ];
        }

        return $splittedList;
    }

    /**
     * @return int
     */
    public function getLimitPerPage()
    {
        return (int)$this->config->getFrontendLinksLimit();
    }

    /**
     * @return bool
     */
    public function isFirstPage()
    {
        if (!$this->getProductCollection()->getCurPage()) {
            return true;
        }

        return $this->getProductCollection()->getCurPage() == 1;
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->config->getFrontendSitemapColumnCount();
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providerRepository->getProviders();
    }

    /**
     * @return ProviderInterface[]
     */
    public function getFrontendProviders()
    {
        $providers         = $this->providerRepository->getProviders();
        $frontendProviders = [];

        foreach ($providers as $provider) {
            /* we need to exclude Category and Product provider
             * because for frontend sitemap will used Category and Product collections
             * that will be processed in another way
             */
            if (get_class($provider) == 'ProductProvider' || get_class($provider) == 'CategoryProvider') {
                continue;
            }

            $frontendProviders[] = $provider;
        }

        return $frontendProviders;
    }

    /**
     * @param ProviderInterface $provider
     *
     * @return DataObject[]
     */
    public function getProviderItems(ProviderInterface $provider)
    {
        $providerItems = $provider->getItems($this->_storeManager->getStore()->getId());

        foreach ($providerItems as $itemKey => $providerItem) {
            if ($this->seoSitemapData->checkIsUrlExcluded($providerItem->getUrl())) {
                unset($providerItems[$itemKey]);
            }
        }

        return $providerItems;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->addBreadcrumbs();
        $this->pageConfig->getTitle()->set($this->config->getFrontendSitemapMetaTitle());
        $this->pageConfig->setKeywords($this->config->getFrontendSitemapMetaKeywords());
        $this->pageConfig->setDescription($this->config->getFrontendSitemapMetaDescription());

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');

        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->escapeHtml($this->config->getFrontendSitemapH1()));
        }

        if ($this->getLimitPerPage()) {
            /** @var Map\Pager $pagerBlock */
            $pagerBlock = $this->getLayout()->getBlock('map.pager');
            $pagerBlock->setLimit($this->getLimitPerPage());
            $pagerBlock->setCollection($this->getProductCollection());
            $pagerBlock->setShowPerPage(false);
            $pagerBlock->setShowAmounts(false);
        } else {
            $this->getLayout()->unsetElement('map.pager');
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     * @return void
     */
    protected function addBreadcrumbs()
    {
        if ($this->config->canShowBreadcrumbs()
            && $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->context->getStoreManager()->getStore()->getBaseUrl(),
            ]);
            $breadcrumbsBlock->addCrumb('cms_page', [
                'label' => $this->config->getFrontendSitemapMetaTitle(),
                'title' => $this->config->getFrontendSitemapMetaTitle(),
            ]);
        }
    }

    /**
     * @return PagerCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductCollection()
    {
        if (empty($this->pagerCollection->getCollection())) {
            $this->pagerCollection->setCollection($this->productBlock->getCollection());
        }

        return $this->pagerCollection->getCollection();
    }
}

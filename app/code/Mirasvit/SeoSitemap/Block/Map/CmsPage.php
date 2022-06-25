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



namespace Mirasvit\SeoSitemap\Block\Map;

use Mirasvit\SeoSitemap\Model\Config\CmsSitemapConfig;
use Mirasvit\SeoSitemap\Model\Config\LinkSitemapConfig;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;

class CmsPage extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\SeoSitemap\Model\Config\CmsSitemapConfig
     */
    private $cmsSitemapConfig;

    /**
     * @var \Mirasvit\SeoSitemap\Model\Config\LinkSitemapConfig
     */
    private $linkSitemapConfig;

    /**
     * @var array
     */
    private $cmsPagesCollection;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $store;

    /**
     * @var array
     */
    private $additionalLinksCollection;
    /**
     * @var PageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * CmsPage constructor.
     * @param CmsSitemapConfig $cmsSitemapConfig
     * @param LinkSitemapConfig $linkSitemapConfig
     * @param PageCollectionFactory $pageCollectionFactory
     * @param Context $context
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        CmsSitemapConfig $cmsSitemapConfig,
        LinkSitemapConfig $linkSitemapConfig,
        PageCollectionFactory $pageCollectionFactory,
        Context $context
    ) {
        $this->cmsSitemapConfig         = $cmsSitemapConfig;
        $this->linkSitemapConfig        = $linkSitemapConfig;
        $this->pageCollectionFactory    = $pageCollectionFactory;
        $this->context                  = $context;
        $this->store                    = $context->getStoreManager()->getStore();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __('Pages');
    }

    /**
     * @return bool
     */
    public function canShowCmsPages()
    {
        $result = false;
        if ($this->cmsSitemapConfig->getIsShowCmsPages()) {
            if ($this->getCollection() || $this->getAdditionalCollection()) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCollection()
    {
        if (empty($this->cmsPagesCollection)) {
            $ignore     = $this->cmsSitemapConfig->getIgnoreCmsPages();
            $collection = $this->pageCollectionFactory->create()
                ->addStoreFilter($this->store)
                ->addFieldToFilter('is_active', true)
                ->addFieldToFilter('main_table.page_id', ['nin' => $ignore]);

            $this->cmsPagesCollection = $this->prepareCmsCollection($collection);
        }

        return $this->cmsPagesCollection;
    }

    /**
     * @return array
     */
    public function getAdditionalCollection()
    {
        if (empty($this->additionalLinksCollection)) {
            $this->additionalLinksCollection = $this->linkSitemapConfig->getAdditionalLinks($this->store->getId());
        }

        return $this->additionalLinksCollection;
    }

    /**
     * @param mixed $collection
     * @return array
     */
    private function prepareCmsCollection($collection)
    {
        $result = [];
        foreach ($collection as $key => $page) {
            $pageData = new \Magento\Framework\DataObject();
            $pageData->setTitle($page->getTitle());
            $pageData->setUrl($this->getCmsPageUrl($page));

            $result[] = $pageData;
        }

        return $result;
    }

    /**
     * @param mixed $page
     * @return string
     */
    private function getCmsPageUrl($page)
    {
        $pageIdentifier = ($page->getHierarchyRequestUrl()) ? $page->getHierarchyRequestUrl() :
            $page->getIdentifier();
        return $this->context->getUrlBuilder()->getUrl(null, ['_direct' => $pageIdentifier]);
    }
}

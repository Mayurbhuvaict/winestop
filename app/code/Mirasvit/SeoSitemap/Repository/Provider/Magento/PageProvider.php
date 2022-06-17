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



namespace Mirasvit\SeoSitemap\Repository\Provider\Magento;

use Magento\Framework\DataObject;
use Magento\Sitemap\Helper\Data as DataHelper;
use Magento\Sitemap\Model\ResourceModel\Cms\PageFactory;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;
use Mirasvit\SeoSitemap\Model\Config\CmsSitemapConfig;
use Mirasvit\SeoSitemap\Model\Config\LinkSitemapConfig;

class PageProvider implements ProviderInterface
{
    /**
     * @var PageFactory
     */
    private $cmsFactory;

    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var CmsSitemapConfig
     */
    private $cmsSitemapConfig;

    /**
     * @var LinkSitemapConfig
     */
    private $linkSitemapConfig;

    /**
     * PageProvider constructor.
     * @param CmsSitemapConfig $cmsSitemapConfig
     * @param LinkSitemapConfig $linkSitemapConfig
     * @param DataHelper $dataHelper
     * @param PageFactory $cmsFactory
     */
    public function __construct(
        CmsSitemapConfig $cmsSitemapConfig,
        LinkSitemapConfig $linkSitemapConfig,
        DataHelper $dataHelper,
        PageFactory $cmsFactory
    ) {
        $this->dataHelper        = $dataHelper;
        $this->cmsFactory        = $cmsFactory;
        $this->cmsSitemapConfig  = $cmsSitemapConfig;
        $this->linkSitemapConfig = $linkSitemapConfig;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'Magento_Cms';
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        return true;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Pages');
    }

    /**
     * @param int $storeId
     * @return array|DataObject
     */
    public function initSitemapItem($storeId)
    {
        return new DataObject([
            'changefreq' => $this->dataHelper->getPageChangefreq($storeId),
            'priority'   => $this->dataHelper->getPagePriority($storeId),
            'collection' => $this->getCmsPages($storeId),
        ]);
    }

    /**
     * @param int $storeId
     * @return array
     */
    private function getCmsPages($storeId)
    {
        $ignore   = $this->cmsSitemapConfig->getIgnoreCmsPages();
        $links    = $this->linkSitemapConfig->getAdditionalLinks($storeId);
        $cmsPages = $this->cmsFactory->create()->getCollection($storeId);

        foreach ($cmsPages as $cmsKey => $cms) {
            if (in_array($cms->getId(), $ignore)) {
                unset($cmsPages[$cmsKey]);
            }

            if ($cms->getUrl() == 'home') {
                $cms->setUrl('');
            }
        }

        if ($links) {
            $cmsPages = array_merge($cmsPages, $links);
        }

        return $cmsPages;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId)
    {
        return [];
    }
}

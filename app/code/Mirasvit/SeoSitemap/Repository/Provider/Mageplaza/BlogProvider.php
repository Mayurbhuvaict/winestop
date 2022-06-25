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



namespace Mirasvit\SeoSitemap\Repository\Provider\Mageplaza;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sitemap\Helper\Data as DataHelper;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class BlogProvider implements ProviderInterface
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * BlogProvider constructor.
     * @param ObjectManagerInterface $objectManager
     * @param DataHelper $sitemapData
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        DataHelper $sitemapData
    ) {
        $this->objectManager = $objectManager;
        $this->dataHelper    = $sitemapData;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'Mageplaza_Blog';
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
        return __('Blog');
    }

    /**
     * @param int $storeId
     * @return array
     */
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

    /**
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId)
    {
        /** @var \Mageplaza\Blog\Helper\Data $helper */
        $helper     = $this->objectManager->get('Mageplaza\Blog\Helper\Data');
        $collection = $helper->getPostCollection(null, null, $storeId);
        $route = $helper->getRoute();
        $items = [];

        foreach ($collection as $key => $post) {
            $items[] = new DataObject([
                'id'         => $post->getId(),
                'url'        => $route . '/' . $post->getUrlKey(),
                'title'      => $post->getName(),
                'updated_at' => $post->getUpdatedAt(),
            ]);
        }

        return $items;
    }
}

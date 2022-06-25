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



namespace Mirasvit\SeoSitemap\Repository\Provider\Aheadworks;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class BlogProvider implements ProviderInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * BlogProvider constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'Aheadworks_Blog';
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        return class_exists('Aheadworks\Blog\Model\Sitemap\ItemsProvider');
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

        $sitemapHelper = $this->objectManager->get('Aheadworks\Blog\Model\Sitemap\ItemsProvider');

        $result[] = $sitemapHelper->getBlogItem($storeId);
        $result[] = $sitemapHelper->getCategoryItems($storeId);
        $result[] = $sitemapHelper->getPostItems($storeId);

        return $result;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId)
    {
        $sitemapHelper             = $this->objectManager->create('Aheadworks\Blog\Helper\Sitemap');
        $urlHelper                 = $this->objectManager->create('Aheadworks\Blog\Helper\Url');
        $categoryCollectionFactory = $this->objectManager->create('Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory');
        $postCollectionFactory     = $this->objectManager->create('Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory');

        $items = [];
        $home  = $sitemapHelper->getBlogItem($storeId)->getCollection();

        if (isset($home[0]) && is_object($home[0])) {
            $items['home'] = new DataObject([
                'name' => 'Blog Home',
                'url'  => $home[0]->getUrl(),
            ]);
        }

        $categoryCollection = $categoryCollectionFactory->create()
            ->addEnabledFilter()
            ->addStoreFilter($storeId);
        foreach ($categoryCollection as $category) {
            $items['cat' . $category->getId()] = new DataObject([
                'name' => $category->getName(),
                'url'  => $urlHelper->getCategoryRoute($category),
            ]);
        }

        $postCollection = $postCollectionFactory->create()
            ->addPublishedFilter()
            ->addStoreFilter($storeId);

        foreach ($postCollection as $post) {
            $items['post' . $post->getId()] = new DataObject([
                'name' => $post->getTitle(),
                'url'  => $urlHelper->getPostRoute($post),
            ]);
        }

        return $items;
    }
}

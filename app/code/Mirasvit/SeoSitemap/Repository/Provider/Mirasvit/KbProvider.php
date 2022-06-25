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

use Magento\Framework\Model\Context;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;
use Magento\Framework\DataObject;

class KbProvider implements ProviderInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * KbProvider constructor.
     * @param ObjectManagerInterface $objectManager
     * @param Context $context
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Context $context
    ) {
        $this->objectManager = $objectManager;
        $this->eventManager  = $context->getEventDispatcher();
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'Mirasvit_Kb';
    }

    /**
     * @return bool
     */
    public function isApplicable()
    {
        return interface_exists('Mirasvit\Kb\Api\Data\SitemapInterface');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return __('Knowledge Base');
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function initSitemapItem($storeId)
    {
        $result = [];

        $this->eventManager->dispatch('core_register_urlrewrite');

        $kbSitemap = $this->objectManager->get('Mirasvit\Kb\Api\Data\SitemapInterface');

        $result[] = $kbSitemap->getBlogItem($storeId);

        if ($categoryItems = $kbSitemap->getCategoryItems($storeId)) {
            $result[] = $categoryItems;
        }

        if ($postItems = $kbSitemap->getPostItems($storeId)) {
            $result[] = $postItems;
        }

        return $result;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getItems($storeId)
    {
        $items = [];
        $sitemapData = $this->initSitemapItem($storeId);
        foreach ($sitemapData as $data) {
            $itemCollection = $data->getCollection();
            foreach ($itemCollection as $item) {
                if (empty($item->getName())) {
                    continue;
                }

                $items[] = new DataObject([
                    'url'        => $item->getUrl(),
                    'title'      => $item->getName(),
                ]);
            }
        }

        return $items;
    }
}

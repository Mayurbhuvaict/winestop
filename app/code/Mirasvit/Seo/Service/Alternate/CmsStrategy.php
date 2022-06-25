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


declare(strict_types=1);

namespace Mirasvit\Seo\Service\Alternate;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Cms\Model\Page as CmsPage;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;
use Mirasvit\Seo\Api\Service\Alternate\UrlInterface;
use Mirasvit\Seo\Helper\Version as VersionHelper;

class CmsStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    protected $url;

    protected $page;

    protected $pageCollectionFactory;

    protected $request;

    protected $version;

    protected $resource;

    protected $alternateConfig;

    protected $storeManager;

    public function __construct(
        UrlInterface $url,
        CmsPage $page,
        CmsPageCollectionFactory $pageCollectionFactory,
        HttpRequest $request,
        VersionHelper $version,
        ResourceConnection $resource,
        AlternateConfig $alternateConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->url                   = $url;
        $this->page                  = $page;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->request               = $request;
        $this->version               = $version;
        $this->resource              = $resource;
        $this->alternateConfig       = $alternateConfig;
        $this->storeManager          = $storeManager;
    }

    public function getStoreUrls(): array
    {
        $storeUrls = $this->url->getStoresCurrentUrl();
        $storeUrls = $this->getAlternateUrl($storeUrls);

        return $storeUrls;
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAlternateUrl(array $storeUrls): array
    {
        $cmsPageId = $this->page->getPageId();
        $alternateGroupInstalled = false;
        /** @var array $cmsStoresIds */
        $cmsStoresIds = $this->page->getStoreId();

        //check if alternate_group exist in cms_page table
        if (($pageObject = $this->pageCollectionFactory->create()->getItemById($cmsPageId))
            && is_object($pageObject) && ($pageObjectData = $pageObject->getData())
            && array_key_exists('alternate_group', $pageObjectData)) {
            $alternateGroupInstalled = true;
        }

        if (!$alternateGroupInstalled) {
            return $storeUrls;
        }

        $cmsCollection = $this->pageCollectionFactory->create()
            ->addFieldToSelect('alternate_group')
            ->addFieldToFilter('page_id', ['eq' => $cmsPageId])
            ->getFirstItem();

        $alternateGroup = $cmsCollection->getAlternateGroup();

        if ($cmsStoresIds[0] == 0 //use if alternate groups configured and use alternate configuration
            && $alternateGroup
            && ($storeId = (int)$this->storeManager->getStore()->getStoreId())
            && $this->alternateConfig->getAlternateHreflang($storeId) == AlternateConfig::ALTERNATE_CONFIGURABLE
            && ($stores = $this->alternateConfig->getAlternateManualConfig($storeId, false))) {
                $cmsPages = $this->getCmsPages($alternateGroup);

            if (count($cmsPages) > 0) {
                $storeUrls = []; // use only links with alternate_group
                $pageStoreData= [];
                foreach ($cmsPages as $page) {
                    $pageStoreData[$page['store_id']] = $page;
                }

                foreach ($stores as $store) {
                    if (isset($this->url->getStores()[$store])) {
                        $page = isset($pageStoreData[$store]) ? $pageStoreData[$store] : $pageStoreData[0];
                        $pageIdentifier = $page['identifier'];
                        $fullAction = $this->request->getFullActionName();

                        $baseStoreUrl = $this->url->getStores()[$store]->getBaseUrl();

                        $storeUrls[$store] = ($fullAction == 'cms_index_index') ? $baseStoreUrl
                            : $baseStoreUrl . $pageIdentifier;
                    }
                }
            }
        } elseif ($alternateGroup && $cmsStoresIds[0] != 0) {
            $cmsPages = $this->getCmsPages($alternateGroup);

            if (count($cmsPages) > 0) {
                $storeUrls = []; // use only links with alternate_group
                foreach ($cmsPages as $page) {
                    if (isset($this->url->getStores()[$page['store_id']])) {
                        $pageIdentifier = $page['identifier'];
                        $fullAction = $this->request->getFullActionName();
                        $baseStoreUrl = $this->url->getStores()[$page['store_id']]->getBaseUrl();
                        $storeUrls[$page['store_id']] = ($fullAction == 'cms_index_index') ? $baseStoreUrl
                            : $baseStoreUrl . $pageIdentifier;
                    }
                }
            }
        }

        return $storeUrls;
    }

    protected function getCmsPages(string $alternateGroup): array
    {
        $cmsCollection = $this->pageCollectionFactory->create()
            ->addFieldToSelect(['alternate_group', 'identifier'])
            ->addFieldToFilter('alternate_group', ['eq' => $alternateGroup])
            ->addFieldToFilter('is_active', true);
        $table = $this->resource->getTableName('cms_page_store');
        $storeTablePageId = ($this->version->isEe()) ? 'row_id' : 'page_id';
        $cmsCollection->getSelect()
            ->join(
                [
                    'storeTable' => $table],
                'main_table.page_id = storeTable.' . $storeTablePageId,
                ['store_id' => 'storeTable.store_id']
            );
        $cmsPages = $cmsCollection->getData();

        return $cmsPages;
    }
}

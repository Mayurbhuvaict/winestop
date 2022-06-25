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


namespace Mirasvit\SeoContent\Service;


use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsCollectionFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PreviewService
{
    private $contentService;

    private $productCollectionFactory;

    private $categoryCollectionFactory;

    private $cmsCollectionFactory;

    private $registry;

    private $storeManager;

    private $productVisibility;

    private $productStatus;

    private $resource;

    private $resultPageFactory;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ContentService $contentService,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        CmsCollectionFactory $cmsCollectionFactory,
        ProductStatus $productStatus,
        ProductVisibility $productVisibility,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        ResultPageFactory $resultPageFactory,
        Registry $registry
    ) {
        $this->contentService            = $contentService;
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->cmsCollectionFactory      = $cmsCollectionFactory;
        $this->productStatus             = $productStatus;
        $this->productVisibility         = $productVisibility;
        $this->storeManager              = $storeManager;
        $this->resource                  = $resource;
        $this->resultPageFactory         = $resultPageFactory;
        $this->registry                  = $registry;
    }

    public function getPreview(TemplateInterface $template, array $ids, ?string $url = null): array
    {
        $storeId  = (int)$template->getStoreIds()[0];

        if ($url && ($entityData = $this->resolveEntityByUrlAndType($url, $template->getRuleType()))) {
            $ids     = [$entityData['entity_id']];
            $storeId = (int)$entityData['store_id'];
        }

        if (!$this->isAbleToProcess($template, $storeId, $url, $ids)) {
            return [];
        }

        $storeId  = $this->resolveStoreId($storeId);
        $template = $this->wrapVariables($template);

        switch ($template->getRuleType()) {
            case TemplateInterface::RULE_TYPE_PRODUCT:

                return $this->getProductsPreviews($template, $storeId, $ids);
            case TemplateInterface::RULE_TYPE_CATEGORY:

                return $this->getCategoriesPreviews($template, $storeId, $ids);
            case TemplateInterface::RULE_TYPE_PAGE:

                return $this->getCmsPreviews($template, $storeId, $ids);
            default:

                return [];
        }
    }

    private function getProductsPreviews(TemplateInterface $template, int $storeId, array $ids): array
    {
        $previews = [];

        $collection = $this->productCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->setVisibility($this->productVisibility->getVisibleInSiteIds())
            ->addFieldToSelect('*');

        if (count($ids)) {
            $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        } else {
            $collection->getSelect()->orderRand()->limit(5);
        }

        $currentProduct = $this->registry->registry('current_product');

        $this->registry->unregister('current_product');

        /** @var ProductInterface $product */
        foreach ($collection as $product) {
            $this->registry->register('current_product', $product);
            $this->fillPageConfig($product);

            $content  = $this->contentService->processContent($template, null, $product, 'product');
            $content1 = clone $content;

            $previews[] = new DataObject([
                'type'         => 'Product',
                'item'         => $product,
                'content'      => $content1
            ]);

            $this->registry->unregister('current_product');
        }

        $this->registry->register('current_product', $currentProduct);

        return $previews;
    }

    private function getCategoriesPreviews(TemplateInterface $template, int $storeId, array $ids): array
    {
        $previews = [];

        $rootCategoryId = $this->storeManager->getStore($storeId)->getRootCategoryId();

        $collection = $this->categoryCollectionFactory->create()
            ->setStoreId($storeId)
            ->addIsActiveFilter()
            ->addFieldToFilter('path', ['like' => "%/{$rootCategoryId}/%"])
            ->addFieldToSelect('*');

        if (count($ids)) {
            $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        } else {
            $collection->getSelect()->orderRand()->limit(5);
        }

        $currentCategory = $this->registry->registry('current_category');

        $this->registry->unregister('current_category');

        /** @var CategoryInterface $category */
        foreach ($collection as $category) {
            $this->registry->register('current_category', $category);
            $this->fillPageConfig($category);

            $content  = $this->contentService->processContent($template, null, null, 'category');
            $content1 = clone $content;

            $previews[] = new DataObject([
                'type'         => 'Category',
                'item'         => $category,
                'content'      => $content1
            ]);

            $this->registry->unregister('current_category');
        }

        $this->registry->register('current_category', $currentCategory);

        return $previews;
    }

    private function getCmsPreviews(TemplateInterface $template, int $storeId, array $ids): array
    {
        $previews = [];

        $collection = $this->cmsCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addFieldToSelect('*');

        if (count($ids)) {
            $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        } else {
            $collection->getSelect()->orderRand()->limit(5);
        }

        foreach ($collection as $cmsPage) {
            $this->registry->register('current_cms_page', $cmsPage);
            $this->fillPageConfig($cmsPage);

            $content  = $this->contentService->processContent($template, null, null, 'cms');
            $content1 = clone $content;

            $previews[] = new DataObject([
                'type'         => 'CMS page',
                'item'         => $cmsPage,
                'content'      => $content1
            ]);

            $this->registry->unregister('current_cms_page');
        }

        return $previews;
    }

    private function resolveEntityByUrlAndType (string $url, int $type): ?array
    {
        $storeId      = null;
        $storeBaseUrl = '';

        foreach ($this->storeManager->getStores() as $store) {
            if (strpos($url, $store->getBaseUrl(UrlInterface::URL_TYPE_LINK, true)) === 0) {
                $storeId      = $store->getId();
                $storeBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_LINK, true);

                break;
            }

            if (strpos($url, $store->getBaseUrl(UrlInterface::URL_TYPE_LINK, false)) === 0) {
                $storeId      = $store->getId();
                $storeBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_LINK, false);

                break;
            }

            continue;
        }

        if (!$storeBaseUrl) {
            return null;
        }

        $path       = str_replace($storeBaseUrl, '', $url);
        $entityType = $this->getEntityType($type);

        if (!$entityType) {
            return null;
        }

        $connection = $this->resource->getConnection();
        $select = $connection->select()->from(
            $this->resource->getTableName('url_rewrite'),
            ['entity_id']
        )->where(
            "request_path = '$path'"
        )->where(
            "redirect_type = 0"
        )->where(
            "store_id = $storeId"
        )->where(
            "entity_type = '$entityType'"
        )->limit(1);

        /** @var \Zend_Db_Statement $query */
        $query  = $select->query();
        $result = $query->fetchAll();

        if (!count($result)) {
            return null;
        }

        return [
            'store_id'  => $storeId,
            'entity_id' => $result[0]['entity_id']
        ];
    }

    private function wrapVariables(TemplateInterface $template): TemplateInterface
    {
        foreach ($template->getData() as $key => $value) {
            if (is_string($value)) {
                $template->setData($key, preg_replace('/([{\[][a-zA-Z_|]*[}\]])/', "#vb#$1#ve#", $value));
            }
        }

        return $template;
    }

    private function getEntityType(int $type): string
    {
        $entityType = '';

        switch ($type) {
            case TemplateInterface::RULE_TYPE_PAGE:
                $entityType = 'cms-page';

                break;
            case TemplateInterface::RULE_TYPE_PRODUCT:
                $entityType = 'product';

                break;
            case TemplateInterface::RULE_TYPE_CATEGORY:
                $entityType = 'category';

                break;
            default:

                break;
        }

        return $entityType;
    }

    private function isAbleToProcess(TemplateInterface $template, int $storeId, string $url, array $ids): bool
    {
        if (!in_array(0, $template->getStoreIds()) && !in_array($storeId, $template->getStoreIds())) {
            return false;
        }

        if ($url && !count($ids)) {
            return false;
        }

        return true;
    }

    private function resolveStoreId(int $storeId): int
    {
        if ($storeId == 0) {
            foreach ($this->storeManager->getStores() as $store) {
                if ($store->getIsActive()) {
                    $storeId = $store->getId();
                    break;
                }
            }
        }

        return (int)$storeId;
    }

    private function fillPageConfig(AbstractModel $model): void
    {
        $pageConfig = $this->resultPageFactory->create()->getConfig();

        /** @var ProductInterface $model */
        if ($model instanceof ProductInterface) {
            $keywords = $model->getKeyword();
        } else {
            $keywords = $model->getKeywords();
        }

        $pageConfig->setMetadata('description', $model->getMetaDescription());
        $pageConfig->setMetadata('keywords', $keywords);
        $pageConfig->setMetadata('title', $model->getMetaTitle());
        $pageConfig->getTitle()->set($model->getMetaTitle());
    }
}

<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Helper;

/**
 * Utilities
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $_customerGroupFactory = null;
    /**
     * @var \Magento\Framework\Module\Manager|null
     */
    protected $_moduleManager = null;
    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    protected $_websiteFactory = null;
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind, \Magento\Framework\App\Helper\Context $context, \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupFactory, \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteFactory)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_customerGroupFactory = $customerGroupFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_moduleManager = $context->getModuleManager();
        parent::__construct($context);
    }
    public function getNotificationMessage()
    {
        $message = __('Get the most of Elasticsearch!') . '<br/>';
        $message .= __('Upgrade to ');
        $tmpMessage = [];
        if (!$this->_moduleManager->isEnabled('Wyomind_ElasticsearchAutocomplete')) {
            $tmpMessage[] = '<a href="tps://www.wyomind.com/magento2/elastic-search-magento.html">Elasticsearch Autocomplete</a>';
        }
        if (!$this->_moduleManager->isEnabled('Wyomind_ElasticsearchMultifacetedAutocomplete')) {
            $tmpMessage[] = '<a href="https://www.wyomind.com/magento2/elasticsearch-multifaceted-autocomplete-magento-2.html">Elasticsearch Multifaceted Autocomplete</a>';
        }
        if (!$this->_moduleManager->isEnabled('Wyomind_ElasticsearchLayeredNavigation')) {
            $tmpMessage[] = '<a href="https://www.wyomind.com/magento2/elasticsearch-layered-navigation-magento-2.html">Elasticsearch Layered Navigation</a>';
        }
        if (!empty($tmpMessage)) {
            return $message . implode(', ', $tmpMessage) . " or the complete <a href='https://www.wyomind.com/magento2/elasticsearch-suite-magento-2.html'>Elasticsearch Suite</a>";
        } else {
            return '';
        }
    }
    /**
     * Get the first storeview id
     * @return string|null
     */
    public function getFirstStoreviewId()
    {
        $firstStore = null;
        $stores = $this->_systemStore->getStoreCollection();
        foreach ($stores as $store) {
            $firstStore = $store->getStoreId();
            break;
        }
        return $firstStore;
    }
    /**
     * Get the index name
     * @param string $storeId
     * @return string
     */
    public function getIndexName($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getFirstStoreviewId();
        }
        $index = $this->_searchIndexNameResolver->getIndexName($storeId, \Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID);
        return $index;
    }
    /**
     * @return array
     */
    public function getBrowseColumns()
    {
        $columns = [];
        $columns['id'] = $this->addBrowseColumn('html', 'id', 'ID', 2);
        $columns['sku'] = $this->addBrowseColumn('html', 'sku', 'Sku', 3);
        $columns['status_value'] = $this->addBrowseColumn('html', 'status_value', 'Status', 4);
        $columns['name'] = $this->addBrowseColumn('html', 'name', 'Product Name', 5);
        $columns['description'] = $this->addBrowseColumn('description', 'description', 'Description', 6);
        $columns['category_ids'] = $this->addBrowseColumn('html', 'category_ids', 'Category IDs', 7);
        $columns['visibility'] = $this->addBrowseColumn('html', 'visibility', 'Visibility', 8);
        $sortOrder = 9;
        // Price according to the Website - Customer group
        $customerGroupCollection = $this->_customerGroupFactory->create();
        $customerGroups = $customerGroupCollection->toOptionArray();
        $websiteCollection = $this->_websiteFactory->create();
        $websites = $websiteCollection->toOptionArray();
        foreach ($websites as $website) {
            foreach ($customerGroups as $customerGroup) {
                $field = 'price_' . $customerGroup['value'] . '_' . $website['value'];
                $title = 'Price - ' . $customerGroup['label'] . ' (' . $website['label'] . ')';
                $columns[$field] = $this->addBrowseColumn('price', $field, $title, $sortOrder);
                $sortOrder++;
            }
        }
        $attributesConfig = $this->_productFieldMapper->getAllAttributesTypes(["websiteId" => 0]);
        foreach ($attributesConfig as $attribute => $parameter) {
            if ((false === array_key_exists('index', $parameter) || array_key_exists('index', $parameter) && $parameter['index'] == 'no') && false === array_key_exists($attribute, $columns) && false === in_array($attribute, ['price', 'price_type', 'price_view', 'price_view_value', 'minimal_price', 'special_price', 'tier_price'], true)) {
                $columns[$attribute] = ['arguments' => ['data' => ['config' => ['dataType' => 'text', 'component' => 'Magento_Ui/js/grid/columns/column', 'componentType' => 'column', 'filter' => 'text', 'sorting' => 'asc', 'label' => ucfirst($attribute), 'sortOrder' => $sortOrder, 'sortable' => false]]], 'attributes' => ['class' => 'Magento\\Ui\\Component\\Listing\\Columns\\Column', 'component' => 'Magento_Ui/js/grid/columns/column', 'name' => $attribute], 'children' => []];
                $sortOrder++;
            }
        }
        return $columns;
    }
    /**
     * @param string $type
     * @param string $field
     * @param string $title
     * @param int $sortOrder
     * @return array
     */
    public function addBrowseColumn($type, $field, $title, $sortOrder)
    {
        $column = [];
        if ($type == 'html') {
            $column = ['arguments' => ['data' => ['config' => ['dataType' => 'text', 'component' => 'Magento_Ui/js/grid/columns/column', 'componentType' => 'column', 'filter' => 'text', 'bodyTmpl' => 'ui/grid/cells/html', 'sorting' => 'asc', 'label' => $title, 'sortOrder' => $sortOrder, 'sortable' => false]]], 'attributes' => ['class' => 'Magento\\Ui\\Component\\Listing\\Columns\\Column', 'component' => 'Magento_Ui/js/grid/columns/column', 'name' => $field], 'children' => []];
        } elseif ($type == 'description') {
            $column = ['arguments' => ['data' => ['config' => ['dataType' => 'text', 'component' => 'Magento_Ui/js/grid/columns/column', 'componentType' => 'column', 'filter' => 'text', 'bodyTmpl' => 'Wyomind_ElasticsearchBrowser/listing/browse/bightml', 'sorting' => 'asc', 'label' => $title, 'sortOrder' => $sortOrder, 'sortable' => false]]], 'attributes' => ['class' => 'Magento\\Ui\\Component\\Listing\\Columns\\Column', 'component' => 'Magento_Ui/js/grid/columns/column', 'name' => $field], 'children' => []];
        } elseif ($type == 'json') {
            $column = ['arguments' => ['data' => ['config' => ['dataType' => 'text', 'component' => 'Magento_Ui/js/grid/columns/column', 'componentType' => 'column', 'filter' => 'text', 'bodyTmpl' => 'Wyomind_ElasticsearchBrowser/listing/browse/json', 'sorting' => 'asc', 'label' => $title, 'sortOrder' => $sortOrder, 'sortable' => false]]], 'attributes' => ['class' => 'Magento\\Ui\\Component\\Listing\\Columns\\Column', 'component' => 'Magento_Ui/js/grid/columns/column', 'name' => $field], 'children' => []];
        } elseif ($type == 'price') {
            $column = ['arguments' => ['data' => ['config' => ['dataType' => 'text', 'component' => 'Magento_Ui/js/grid/columns/column', 'componentType' => 'column', 'filter' => 'textRange', 'sorting' => 'asc', 'label' => $title, 'sortOrder' => $sortOrder, 'sortable' => false]]], 'attributes' => ['class' => 'Magento\\Catalog\\Ui\\Component\\Listing\\Columns\\Price', 'component' => 'Magento_Ui/js/grid/columns/column', 'name' => $field], 'children' => []];
        } elseif ($type == 'image') {
            $column = ['arguments' => ['data' => ['config' => ['altField' => $field, 'add_field' => true, 'dataType' => 'text', 'component' => 'Magento_Ui/js/grid/columns/column', 'componentType' => 'column', 'bodyTmpl' => 'Wyomind_ElasticsearchBrowser/listing/browse/image', 'has_preview' => 1, 'label' => $title, 'sortOrder' => $sortOrder, 'sortable' => false]]], 'attributes' => ['class' => 'Magento\\Ui\\Component\\Listing\\Columns\\Column', 'name' => $field], 'children' => []];
        } elseif ($type == 'url') {
            $column = ['arguments' => ['data' => ['config' => ['altField' => $field, 'add_field' => true, 'dataType' => 'text', 'component' => 'Magento_Ui/js/grid/columns/column', 'componentType' => 'column', 'bodyTmpl' => 'Wyomind_ElasticsearchBrowser/listing/browse/url', 'has_preview' => 1, 'filter' => 'text', 'label' => $title, 'sortOrder' => $sortOrder, 'sortable' => false]]], 'attributes' => ['class' => 'Magento\\Ui\\Component\\Listing\\Columns\\Column', 'name' => $field], 'children' => []];
        }
        return $column;
    }
}
<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_SERVER_HOSTNAME = '_server_hostname';
    const XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_SERVER_PORT = '_server_port';
    const XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_ENABLE_AUTH = '_enable_auth';
    const XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_SERVER_TIMEOUT = '_server_timeout';
    const XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_INDEX_PREFIX = '_index_prefix';
    const XML_PATH_ELASTICSEARCHBROWSER_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION = 'wyomind_elasticsearchbrowser/debug/backend_notification_on_elasticsearch_sever_fail';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig = null;
    /**
     * @var string|null
     */
    protected $_enginePath = null;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->getEnginePath();
    }
    /**
     * Get the config path according to the search engine
     * - Elasticsearch: catalog/search/elasticsearch
     * - Elasticsearch 5.0+: catalog/search/elasticsearch5
     */
    public function getEnginePath()
    {
        $engine = $this->getStoreConfig(\Magento\CatalogSearch\Model\ResourceModel\EngineProvider::CONFIG_ENGINE_PATH);
        return $this->_enginePath = 'catalog/search/' . $engine;
    }
    /**
     * Get value from store config
     * @param string $key
     * @param null|string $scopeId
     * @return mixed
     */
    public function getStoreConfig($key, $scopeId = null)
    {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        if (!$scopeId) {
            $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }
        return $this->scopeConfig->getValue($key, $scope, $scopeId);
    }
    // Elasticsearch Servers (host:port)
    public function getServers($scopeId = null)
    {
        return explode(',', $this->getStoreConfig($this->_enginePath . self::XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_SERVER_HOSTNAME, $scopeId));
    }
    // Is a verified host
    public function isVerifyHost($scopeId = null)
    {
        return (bool) $this->getStoreConfig($this->_enginePath . self::XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_ENABLE_AUTH, $scopeId);
    }
    // Connection timeout in seconds
    public function getConnectTimeout($scopeId = null)
    {
        return $this->getStoreConfig($this->_enginePath . self::XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_SERVER_TIMEOUT, $scopeId);
    }
    // Index prefix used to avoid potential collisions
    public function getIndexPrefix($scopeId = null)
    {
        return $this->getStoreConfig($this->_enginePath . self::XML_PATH_CATALOG_SEARCH_ELASTICSEARCH_INDEX_PREFIX, $scopeId);
    }
    // Backend notification
    public function isServerStatusBackendNotificationEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_ELASTICSEARCHBROWSER_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION);
    }
}
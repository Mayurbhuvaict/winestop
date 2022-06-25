<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Model;

class Client
{
    /**
     * @var \Elasticsearch\Client
     */
    protected $_client = null;
    /**
     * @var \Elasticsearch\ClientBuilder
     */
    protected $_clientBuilder = null;
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_clientBuilder = \Elasticsearch\ClientBuilder::create();
    }
    /**
     * Initializes client
     */
    public function init()
    {
        $hosts = $this->_configHelper->getServers();
        $this->_client = $this->_clientBuilder->create()->setHosts($hosts)->build();
    }
    public function info()
    {
        return $this->_client->info($this->getParams());
    }
    /**
     * Request matching documents of given type in specified index with optional params
     *
     * @param string|array $indices
     * @param array $types
     * @param array $params
     * @return array
     */
    public function query($indices, $types, array $params = [])
    {
        $params['index'] = implode(',', (array) $indices);
        $params['type'] = implode(',', (array) $types);
        return $this->_client->search($this->buildParams($params));
    }
    /**
     * @param array $params
     * @return array
     */
    protected function buildParams(array $params = [])
    {
        return array_merge($this->getParams(), $params);
    }
    public function getByIds($indices, $type, $ids)
    {
        $params['index'] = implode(',', (array) $indices);
        $params['type'] = $type;
        $params['body'] = ['ids' => $ids];
        return $this->_client->mget($this->buildParams($params));
    }
    /**
     * @return array
     */
    public function getParams()
    {
        return ['client' => ['verify' => $this->_configHelper->isVerifyHost(), 'connect_timeout' => $this->_configHelper->getConnectTimeout()]];
    }
}
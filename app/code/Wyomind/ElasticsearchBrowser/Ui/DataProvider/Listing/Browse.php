<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Ui\DataProvider\Listing;

/**
 * Class Browse
 * @package Wyomind\ElasticsearchBrowser\Ui\DataProvider\Listing
 */
class Browse extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var int
     */
    protected $_size = 20;
    /**
     * @var int
     */
    protected $_offset = 1;
    /**
     * @var array
     */
    protected $_likeFilters = [];
    /**
     * @var array
     */
    protected $_rangeFilters = [];
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind, $name, $primaryFieldName, $requestFieldName, array $meta = [], array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    public function setLimit($offset, $size)
    {
        $this->_size = $size;
        $this->_offset = $offset;
    }
    public function getData()
    {
        list($indice, $storeId) = $this->_sessionHelper->getBrowseData();
        if ($indice == null) {
            return ['totalRecords' => 0, 'items' => []];
        }
        $query = [];
        foreach ($this->_likeFilters as $field => $value) {
            $query['bool']['must'][] = ['match' => [$field => $value]];
        }
        foreach ($this->_rangeFilters as $field => $fromTo) {
            $query['bool']['filter'][] = ['range' => [$field => $fromTo]];
        }
        $params = ['body' => ['from' => ($this->_offset - 1) * $this->_size, 'size' => $this->_size, 'query' => $query]];
        try {
            $this->_client->init();
            $info = $this->_client->info();
            $serverVersion = $info['version']['number'];
            if (empty($params['body']['query'])) {
                // ES 5.x
                $query = new \stdClass();
                $params['body']['query'] = ['match_all' => $query];
                // ES 2.x
                if (version_compare($serverVersion, '5.0.0') < 0) {
                    unset($params['body']['query']);
                }
            }
            $type = \Magento\Elasticsearch\Model\Config::ELASTICSEARCH_TYPE_DOCUMENT;
            $response = $this->_client->query($indice, $type, $params);
            $docs = [];
            foreach ($response['hits']['hits'] as $doc) {
                $doc['_source']['id'] = $doc['_id'];
                $docs[] = $doc['_source'];
            }
            $count = $response['hits']['total'];
            $result = ['count' => $count, 'docs' => $docs];
        } catch (\Exception $e) {
            return ['totalRecords' => 0, 'items' => []];
        }
        $this->_sessionHelper->setBrowseData([$indice, $storeId]);
        $return = ['totalRecords' => $result['count'], 'items' => array_values($result['docs'])];
        return $return;
    }
    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return mixed|void
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getConditionType() == 'like') {
            $this->_likeFilters[$filter->getField()] = substr($filter->getValue(), 1, -1);
        } elseif ($filter->getConditionType() == 'eq') {
            $this->_likeFilters[$filter->getField()] = $filter->getValue();
        } elseif ($filter->getConditionType() == 'finset') {
            $this->_likeFilters[$filter->getField() . '_ids'] = $filter->getValue();
        } elseif ($filter->getConditionType() == 'gteq') {
            $this->_rangeFilters[$filter->getField()]['from'] = $filter->getValue();
        } elseif ($filter->getConditionType() == 'lteq') {
            $this->_rangeFilters[$filter->getField()]['to'] = $filter->getValue();
        }
    }
    ############################################################################
    public function addField($field, $alias = null)
    {
    }
    public function count()
    {
    }
    public function getSearchResult()
    {
    }
    public function removeField($field, $isAlias = false)
    {
    }
    public function removeAllFields()
    {
    }
}
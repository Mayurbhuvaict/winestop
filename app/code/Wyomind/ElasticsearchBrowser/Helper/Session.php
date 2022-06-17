<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Helper;

class Session
{
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * @return array
     */
    public function getBrowseData()
    {
        $indice = $this->_request->getParam('indice');
        $storeId = $this->_request->getParam('storeId');
        if ($indice == null) {
            // use cache
            $cache = $this->_session->getElasticsearchbrowserBrowseCache();
            if ($cache != null) {
                list($indice, $storeId) = $cache;
            } else {
                $indice = $this->_dataHelper->getIndexName($storeId);
            }
        } else {
            $indice = $this->_dataHelper->getIndexName($storeId);
        }
        return [$indice, $storeId];
    }
    /**
     * @param $data
     */
    public function setBrowseData($data)
    {
        $this->_session->setElasticsearchbrowserBrowseCache($data);
    }
    /**
     * Store the list of ids to reindex
     * e.g: before the category update > store the current product related to the category
     * @param string $type
     * @param array $ids
     */
    public function setIdsToReindex($type, $ids)
    {
        $this->_session->setElasticsearchbrowserIdsToReindex([$type => $ids]);
    }
}
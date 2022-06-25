<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Plugin\Ui\Config;

class Data
{
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    public function aroundGet($subject, $proceed, $path = null, $default = null)
    {
        $data = $proceed($path, $default);
        if ($path == 'elasticsearchbrowser_browse') {
            list($indice, $storeId) = $this->_sessionHelper->getBrowseData();
            // is still no indice, no change
            if ($indice == null) {
                return $data;
            }
            $columns =& $data['children']['columns']['children'];
            $columns = array_merge($columns, $this->_dataHelper->getBrowseColumns());
        }
        return $data;
    }
}
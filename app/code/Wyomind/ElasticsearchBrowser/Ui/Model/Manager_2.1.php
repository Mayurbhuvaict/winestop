<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Ui\Model;

class Manager extends \Magento\Ui\Model\Manager
{
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind, \Magento\Framework\View\Element\UiComponent\Config\Provider\Component\Definition $componentConfigProvider, \Magento\Framework\View\Element\UiComponent\Config\DomMergerInterface $domMerger, \Magento\Framework\View\Element\UiComponent\Config\ReaderFactory $readerFactory, \Magento\Framework\View\Element\UiComponent\ArrayObjectFactory $arrayObjectFactory, \Magento\Framework\View\Element\UiComponent\Config\FileCollector\AggregatedFileCollectorFactory $aggregatedFileCollectorFactory, \Magento\Framework\Config\CacheInterface $cache, \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $construct = "__construct";
        // in order to bypass the compiler
        parent::$construct($componentConfigProvider, $domMerger, $readerFactory, $arrayObjectFactory, $aggregatedFileCollectorFactory, $cache, $argumentInterpreter);
    }
    public function prepareData($name)
    {
        parent::prepareData($name);
        if ($name == 'elasticsearchbrowser_browse') {
            $data = $this->getData($name);
            list($indice, $storeId) = $this->_sessionHelper->getBrowseData();
            if ($indice == null) {
                return $this;
            }
            $columns =& $data['elasticsearchbrowser_browse']['children']['columns']['children'];
            $columns = array_merge($columns, $this->_dataHelper->getBrowseColumns());
            $this->componentsData->offsetSet($name, $data);
        }
        return $this;
    }
}
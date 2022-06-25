<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Ui\Component\Listing\Browse\Column;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind, \Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, array $components = [], array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        list($indice, $storeId) = $this->_sessionHelper->getBrowseData();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $item[$name]['raw'] = ['href' => "javascript:void(require(['elasticsearchbrowser_browse'], function (browse) { browse.raw('" . $this->_urlBuilder->getUrl('elasticsearchbrowser/browse/raw', ['indice' => $indice, 'storeId' => $storeId, 'type' => \Magento\Elasticsearch\Model\Config::ELASTICSEARCH_TYPE_DOCUMENT, 'id' => $item['id']]) . "'); }))", 'label' => __('Raw data'), 'hidden' => false];
                $item[$name]['edit'] = ['href' => $this->_urlBuilder->getUrl('catalog/product/edit', ['id' => $item['id']]), 'label' => __('Edit')];
            }
        }
        return $dataSource;
    }
}
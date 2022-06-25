<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Ui\Component\Listing\Column;

class Link extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory
     */
    protected $feedsCollectionFactory;
    /**
     * @var string
     */
    protected $fieldKey;
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory, array $components = [], array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->feedsCollectionFactory = $feedsCollectionFactory;
        $this->fieldKey = 'link';
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->fieldKey] = $this->prepareItem($item);
            }
        }
        return $dataSource;
    }
    /**
     * Get data
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        $feedsCollection = $this->feedsCollectionFactory->create();
        $feedsCollection->addFieldToFilter('simplegoogleshopping_id', $item['simplegoogleshopping_id']);
        if ($feedsCollection->getSize()) {
            $feed = $feedsCollection->getFirstItem();
            $content = $this->sgsHelper->generationStats($feed);
        }
        return $content;
    }
}
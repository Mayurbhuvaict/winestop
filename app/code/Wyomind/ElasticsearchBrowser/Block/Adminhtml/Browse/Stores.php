<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Block\Adminhtml\Browse;

/**
 * Class Stores
 */
class Stores extends \Magento\Framework\View\Element\Template
{
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind, \Magento\Framework\View\Element\Template\Context $context, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context, $data);
    }
    /**
     * Get all stores/websites/storeviews
     * @return array
     */
    public function getStores()
    {
        $stores = $this->_systemStore->getStoresStructure(true);
        return $stores;
    }
    /**
     * Get the current storeview
     * @return mixed
     */
    public function getSelectedStoreId()
    {
        list($indice, $storeId) = $this->_sessionHelper->getBrowseData();
        return $storeId;
    }
    /**
     * @return \Wyomind\ElasticsearchBrowser\Helper\Data
     */
    public function getDataHelper()
    {
        return $this->_dataHelper;
    }
}
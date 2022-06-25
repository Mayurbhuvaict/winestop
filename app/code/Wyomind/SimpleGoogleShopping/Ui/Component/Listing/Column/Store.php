<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Ui\Component\Listing\Column;

class Store extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var string
     */
    protected $storeKey;
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, array $components = [], array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->storeKey = 'store_id';
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
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
        $origStores = [];
        if (!empty($item[$this->storeKey])) {
            $origStores = $item[$this->storeKey];
        }
        if (empty($origStores)) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }
        if (in_array(0, $origStores) && count($origStores) == 1) {
            return __('All Store Views');
        }
        $data = $this->systemStore->getStoresStructure(false, $origStores);
        foreach ($data as $website) {
            $content .= '<b>' . $website['label'] . '</b><br/>';
            foreach ($website['children'] as $group) {
                $content .= str_repeat('&nbsp;', 3) . '<b><i>' . $this->escaper->escapeHtml($group['label']) . '</i></b><br/>';
                foreach ($group['children'] as $store) {
                    $content .= str_repeat('&nbsp;', 6) . $this->escaper->escapeHtml($store['label']) . '<br/>';
                }
            }
        }
        return $content;
    }
}
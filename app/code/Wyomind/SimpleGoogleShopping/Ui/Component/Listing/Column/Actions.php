<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Ui\Component\Listing\Column;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, array $components = [], array $data = [])
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['simplegoogleshopping_id'])) {
                    if ($this->authorization->isAllowed('Wyomind_SimpleGoogleShopping::edit')) {
                        $item[$name]['edit'] = ['href' => $this->urlBuilder->getUrl('simplegoogleshopping/feeds/edit', ['id' => $item['simplegoogleshopping_id']]), 'label' => __('Edit')];
                    }
                    if ($this->authorization->isAllowed('Wyomind_SimpleGoogleShopping::generate')) {
                        $item[$name]['generate'] = ['href' => $this->urlBuilder->getUrl('simplegoogleshopping/feeds/generate', ['id' => $item['simplegoogleshopping_id']]), 'label' => __('Generate'), 'confirm' => ['title' => __('Generate data feed'), 'message' => __('Generate a data feed can take a while. Are you sure you want to generate it now?')]];
                    }
                    $item[$name]['preview'] = ['href' => $this->urlBuilder->getUrl('simplegoogleshopping/feeds/preview', ['simplegoogleshopping_id' => $item['simplegoogleshopping_id']]), 'label' => __('Preview (%1 items)', $this->licenseHelper->getStoreConfig('simplegoogleshopping/system/preview')), 'target' => '_blank'];
                    if ($this->authorization->isAllowed('Wyomind_SimpleGoogleShopping::delete')) {
                        $item[$name]['delete'] = ['href' => $this->urlBuilder->getUrl('simplegoogleshopping/feeds/delete', ['id' => $item['simplegoogleshopping_id']]), 'label' => __('Delete'), 'confirm' => ['title' => __('Delete a feed'), 'message' => __('Are you sure you want to delete the feed <b>%1</b>', $item['simplegoogleshopping_filename'])]];
                    }
                }
            }
        }
        return $dataSource;
    }
}
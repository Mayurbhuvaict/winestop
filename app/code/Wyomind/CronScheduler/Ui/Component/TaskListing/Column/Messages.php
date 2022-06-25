<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\CronScheduler\Ui\Component\TaskListing\Column;

/**
 * Renderer for the "messages" columns in the tasks listings
 * @version 1.0.0
 */
class Messages extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var string
     */
    private $_viewUrl = \Wyomind\CronScheduler\Helper\Url::TASK_VIEW;
    public function __construct(\Wyomind\CronScheduler\Helper\Delegate $wyomind, \Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, array $components = [], array $data = [])
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
            $url = $this->urlBuilder->getUrl($this->_viewUrl);
            foreach ($dataSource['data']['items'] as &$item) {
                $messages = nl2br($item[$this->getData('name')]);
                if (strlen($messages) > 200) {
                    $messages = substr($messages, 0, 200) . "...";
                }
                $item[$this->getData('name')] = $messages;
            }
        }
        return $dataSource;
    }
}
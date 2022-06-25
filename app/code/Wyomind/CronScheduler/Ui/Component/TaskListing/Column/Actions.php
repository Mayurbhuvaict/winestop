<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\CronScheduler\Ui\Component\TaskListing\Column;

/**
 * Task listing actions
 * @version 1.0.0
 */
class Actions extends \Magento\Ui\Component\Listing\Columns\Column
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
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['schedule_id']) && $item['status'] != \Magento\Cron\Model\Schedule::STATUS_PENDING) {
                    $url = $this->urlBuilder->getUrl($this->_viewUrl);
                    $item[$name]['view_more'] = ['href' => "javascript:void(require(['cs_task'], function (task) { task.view('" . $url . "','" . $item['schedule_id'] . "'); }))", 'label' => __('View More')];
                }
            }
        }
        return $dataSource;
    }
}
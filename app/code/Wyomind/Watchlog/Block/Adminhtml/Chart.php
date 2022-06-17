<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Block\Adminhtml;

/**
 * Class Chart
 * @package Wyomind\Watchlog\Block\Adminhtml
 */
class Chart extends \Magento\Backend\Block\Template
{
    /**
     * @var \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory
     */
    protected $_attemptsCollectionFactory = null;
    public function __construct(\Wyomind\Watchlog\Helper\Delegate $wyomind, \Magento\Backend\Block\Template\Context $context, \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory $attemptsCollectionFactory, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_attemptsCollectionFactory = $attemptsCollectionFactory;
        parent::__construct($context, $data);
        $this->setTemplate('chart.phtml');
    }
    /**
     * Get attempts data for the last X days (X is a value from the configuration of the extension)
     * @return array A list of javascript dates, the number of success and failure
     */
    public function getChartDataSummaryMonth()
    {
        $data = [];
        $headers = [__('Date'), __('Success'), __('Failed')];
        $data[] = $headers;
        $tmpData = [];
        $nbDays = $this->watchlogHelper->getDefaultConfig("watchlog/settings/history");
        $currentTimestamp = $this->_datetime->gmtTimestamp() + $this->_datetime->getGmtOffset();
        $yesterdayMonthTimestamp = $currentTimestamp - ($nbDays - 1) * 24 * 60 * 60;
        while ($yesterdayMonthTimestamp <= $currentTimestamp) {
            $key = $this->_datetime->date('Y-m-d', $yesterdayMonthTimestamp);
            $tmpData[$key] = [\Wyomind\Watchlog\Helper\Data::FAILURE => 0, \Wyomind\Watchlog\Helper\Data::SUCCESS => 0];
            $yesterdayMonthTimestamp += 24 * 60 * 60;
        }
        $collection = $this->_attemptsCollectionFactory->create()->getSummaryMonth();
        foreach ($collection as $entry) {
            $key = $this->_datetime->date('Y-m-d', strtotime($entry->getDate()) + $this->_datetime->getGmtOffset());
            if (!isset($tmpData[$key])) {
                $tmpData[$key] = [\Wyomind\Watchlog\Helper\Data::FAILURE => 0, \Wyomind\Watchlog\Helper\Data::SUCCESS => 0];
            }
            $tmpData[$key][$entry->getStatus()] = $entry->getNb();
        }
        ksort($tmpData);
        foreach ($tmpData as $date => $entry) {
            $data[] = ["#new Date('" . $date . "')#", (int) $entry[\Wyomind\Watchlog\Helper\Data::SUCCESS], (int) $entry[\Wyomind\Watchlog\Helper\Data::FAILURE]];
        }
        return $data;
    }
    /**
     * Get attempts data for the last 24 hours
     * @return array A list of javascript dates, the number of success and failure
     */
    public function getChartDataSummaryDay()
    {
        $data = [];
        $headers = [__('Date'), __('Success'), __('Failed')];
        $data[] = $headers;
        $tmpData = [];
        $currentTimestamp = $this->_datetime->gmtTimestamp() + $this->_datetime->getGmtOffset();
        $yesterdayTimestamp = $currentTimestamp - 23 * 60 * 60;
        while ($yesterdayTimestamp <= $currentTimestamp) {
            $key = $this->_datetime->date('M d, Y H:00:00', $yesterdayTimestamp);
            $tmpData[$key] = [\Wyomind\Watchlog\Helper\Data::FAILURE => 0, \Wyomind\Watchlog\Helper\Data::SUCCESS => 0];
            $yesterdayTimestamp += 60 * 60;
        }
        $collection = $this->_attemptsCollectionFactory->create()->getSummaryDay();
        foreach ($collection as $entry) {
            $key = $this->_datetime->date('M d, Y H:00:00', strtotime($entry->getDate()) + $this->_datetime->getGmtOffset());
            if (!isset($tmpData[$key])) {
                $tmpData[$key] = [\Wyomind\Watchlog\Helper\Data::FAILURE => 0, \Wyomind\Watchlog\Helper\Data::SUCCESS => 0];
            }
            $tmpData[$key][$entry->getStatus()] = $entry->getNb();
        }
        foreach ($tmpData as $date => $entry) {
            $data[] = ["#new Date('" . $date . "')#", (int) $entry[\Wyomind\Watchlog\Helper\Data::SUCCESS], (int) $entry[\Wyomind\Watchlog\Helper\Data::FAILURE]];
        }
        return $data;
    }
}
<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Cron;

/**
 * Class History
 * @package Wyomind\Watchlog\Cron
 */
class History
{
    /**
     * @var \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory
     */
    protected $_attemptsCollectionFactory;
    public function __construct(\Wyomind\Watchlog\Helper\Delegate $wyomind, \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory $attemptsCollectionFactory)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_attemptsCollectionFactory = $attemptsCollectionFactory;
    }
    /**
     * @param \Magento\Cron\Model\Schedule|null $schedule
     * @throws \Exception
     */
    public function purge(\Magento\Cron\Model\Schedule $schedule = null)
    {
        try {
            $timestamp = $this->_datetime->gmtTimestamp();
            $historyLength = $this->_watchlogHelper->getDefaultConfig("watchlog/settings/history");
            $deleteBefore = $timestamp - $historyLength * 60 * 60 * 24;
            if ($historyLength != 0) {
                $this->_logger->notice("-------------------- PURGE PROCESS --------------------");
                $this->_logger->notice("-- current date : " . $this->_datetime->gmtDate('Y-m-d H:i:s', $timestamp));
                $this->_logger->notice("-- deleting row before : " . $this->_datetime->gmtDate('Y-m-d H:i:s', $deleteBefore));
                $nbDeleted = $this->_attemptsCollectionFactory->create()->purge($deleteBefore);
                $this->_logger->notice("-- {$nbDeleted} rows deleted");
            }
        } catch (\Exception $e) {
            if ($schedule) {
                $schedule->setStatus('failed');
                $schedule->setMessage($e->getMessage());
                $schedule->save();
            }
            $this->_logger->notice("MASSIVE ERROR ! ");
            $this->_logger->notice($e->getMessage());
        }
    }
}
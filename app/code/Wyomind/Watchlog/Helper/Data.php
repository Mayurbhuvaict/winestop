<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Helper;

/**
 * Class Data
 * @package Wyomind\Watchlog\Helper
 * 
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var int
     */
    const SUCCESS = 1;

    /**
     * @var int
     */
    const FAILURE = 0;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_datetime;

    /**
     * @var \Wyomind\Framework\Helper\Module
     */
    protected $_framework;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Wyomind\Watchlog\Model\AttemptsFactory
     */
    protected $_attemptsModelFactory;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    protected $_inboxFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Wyomind\Framework\Helper\Module $framework
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Wyomind\Watchlog\Model\AttemptsFactory $attemptsModelFactory
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Wyomind\Framework\Helper\Module $framework,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Wyomind\Watchlog\Model\AttemptsFactory $attemptsModelFactory,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
    
        parent::__construct($context);
        $this->_datetime = $datetime;
        $this->_framework = $framework;
        $this->_messageManager = $messageManager;
        $this->_attemptsModelFactory = $attemptsModelFactory;
        $this->_backendHelper = $backendHelper;
        $this->_inboxFactory = $inboxFactory;
    }

    /**
     * @param $key
     * @return string
     */
    public function getDefaultConfig($key)
    {
        return $this->_framework->getDefaultConfig($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setDefaultConfig($key, $value)
    {
        $this->_framework->setDefaultConfig($key, $value);
    }

    public function checkWarning()
    {
        $failedLimit = $this->getDefaultConfig('watchlog/settings/failed_limit');
        $percent = $this->_attemptsModelFactory->create()->getFailedPercentFromDate();
        $notificationDetails = $this->getDefaultConfig('watchlog/settings/notification_details');
        if ($percent > $failedLimit) {
            $this->_messageManager->addError(sprintf(__($notificationDetails), number_format($percent * 100, 2, '.', '')));
        }
    }

    public function checkNotification()
    {
        $lastNotification = $this->getDefaultConfig('watchlog/settings/last_notification');
        $failedLimit = $this->getDefaultConfig('watchlog/settings/failed_limit');

        $percent = $this->_attemptsModelFactory->create()->getCollection()->getFailedPercentFromDate($lastNotification);

        if ($percent > $failedLimit) {
            // add notification in inbox
            $notificationTitle = $this->getDefaultConfig('watchlog/settings/notification_title');
            $notificationDescription = $this->getDefaultConfig('watchlog/settings/notification_description');
            $notificationLink = $this->_backendHelper->getUrl('/watchlog/basic/index');

            $date = $this->_datetime->gmtDate('Y-m-d H:i:s');

            $notify = $this->_inboxFactory->create();
            $item = $notify->getCollection()->addFieldToFilter('title', ['eq' => 'Watchlog security warning'])->addFieldToFilter('is_remove', ['eq' => 0]);
            $data = $item->getLastItem()->getData();

            if (isset($data['notification_id'])) {
                $notify->load($data['notification_id']);
                $notify->setUrl($notificationLink);
                $notify->setDescription(sprintf(__($notificationDescription), number_format($percent * 100, 2, ".", ""), $notificationLink));
                $notify->setData('is_read', 0)->save();
            } else {
                $notify->setTitle(__($notificationTitle));
                $notify->setUrl($notificationLink);
                $notify->setDescription(sprintf(__($notificationDescription), number_format($percent * 100, 2, ".", ""), $notificationLink));
                $notify->setSeverity(1);
                $notify->save();
            }
            $this->setDefaultConfig('watchlog/settings/last_notification', $date);
        }
    }
}

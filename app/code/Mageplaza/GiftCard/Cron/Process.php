<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Cron;

use DateTime;
use DateTimeZone;
use Exception;
use Magento\Backend\Model\Auth;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Stdlib\DateTime as DateTimeStdlib;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCard\Action;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\HistoryFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Process
 * @package Mageplaza\GiftCard\Cron
 */
class Process
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Resource
     */
    protected $_resource;

    /**
     * @var DateTimeStdlib
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var AdapterInterface
     */
    protected $_connection;

    /**
     * @var CollectionFactory
     */
    protected $_collection;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Auth
     */
    protected $_auth;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * Process constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     * @param DateTimeStdlib $dateTime
     * @param TimezoneInterface $localeDate
     * @param CollectionFactory $collectionFactory
     * @param Data $dataHelper
     * @param LoggerInterface $logger
     * @param Auth $_auth
     * @param HistoryFactory $historyFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        DateTimeStdlib $dateTime,
        TimezoneInterface $localeDate,
        CollectionFactory $collectionFactory,
        Data $dataHelper,
        LoggerInterface $logger,
        Auth $_auth,
        HistoryFactory $historyFactory
    ) {
        $this->_storeManager  = $storeManager;
        $this->_resource      = $resource;
        $this->_dateTime      = $dateTime;
        $this->_localeDate    = $localeDate;
        $this->_collection    = $collectionFactory;
        $this->_dataHelper    = $dataHelper;
        $this->_logger        = $logger;
        $this->_auth          = $_auth;
        $this->historyFactory = $historyFactory;
    }

    /**
     * process gift card status & email
     * @throws Exception
     */
    public function execute()
    {
        $this->expireGiftCard()->sendToRecipient();
    }

    /**
     * Expire Gift Card depend on the website timezone
     *
     * @return $this
     */
    public function expireGiftCard()
    {
        $connection = $this->_getConnection();
        $tableName  = $this->_resource->getTableName('mageplaza_giftcard');
        /** @var Website $website */
        foreach ($this->_storeManager->getWebsites(true) as $website) {
            $timestamp    = $this->_localeDate->scopeTimeStamp($website->getDefaultStore());
            $currDate     = $this->_dateTime->formatDate($timestamp, false);
            $currDateExpr = $connection->quote($currDate);
            $dataFormat   = $connection->getDateFormatSql($currDateExpr, '%Y-%m-%d');

            // timestamp is locale based
            $where = [
                'status'          => Status::STATUS_ACTIVE,
                'store_id IN (?)' => $website->getStoreIds(),
                'expired_at < ?'  => $dataFormat
            ];

            $giftCardIds = $connection->select()->from([$tableName], ['giftcard_id'])
                ->where('status = ?', Status::STATUS_ACTIVE)
                ->where('store_id IN (?)', $website->getStoreIds())
                ->where('expired_at < ?', $dataFormat);
            $giftCardIds = $connection->fetchCol($giftCardIds);

            $connection->update($tableName, ['status' => Status::STATUS_EXPIRED], $where);
            $this->updateHistory($giftCardIds);
        }

        return $this;
    }

    /**
     * @param array $ids
     */
    public function updateHistory($ids)
    {
        $collection = $this->_collection->create()
            ->addFieldToFilter('giftcard_id', ['in' => $ids]);

        foreach ($collection as $giftCard) {
            $giftCard->setAction(Action::ACTION_EXPIRE);
            $giftCard->setActionVars(['auth' => $this->_auth->getUser()->getName()]);
            $this->historyFactory->create()->setGiftCard($giftCard)->save();
        }
    }

    /**
     * Send Gift Card To Recipient
     *
     * @return $this
     * @throws Exception
     */
    public function sendToRecipient()
    {
        $now        = date('Y-m-d', strtotime('+1 day'));
        $collection = $this->_collection->create()
            ->addFieldToFilter('status', Status::STATUS_ACTIVE)
            ->addFieldToFilter('is_sent', 0)
            ->addFieldToFilter('delivery_date', ['notnull' => true])
            ->addFieldToFilter('delivery_date', ['lteq' => $now])
            ->setPageSize(100)
            ->setCurPage(1);

        /** @var GiftCard $giftCard */
        foreach ($collection as $giftCard) {
            $timezone     = $giftCard->getTimezone()
                ? new DateTimeZone($giftCard->getTimezone())
                : $this->_dataHelper->getGiftCardTimeZone($giftCard);
            $currentDate  = (new DateTime(null, $timezone))->format('Y-m-d');
            $deliveryDate = (new DateTime($giftCard->getDeliveryDate()))->format('Y-m-d');
            if ($deliveryDate === $currentDate) {
                try {
                    $giftCard->setData('send_to_recipient', true)
                        ->save();
                } catch (Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve write connection instance
     *
     * @return bool|AdapterInterface
     */
    protected function _getConnection()
    {
        if ($this->_connection === null) {
            $this->_connection = $this->_resource->getConnection();
        }

        return $this->_connection;
    }
}

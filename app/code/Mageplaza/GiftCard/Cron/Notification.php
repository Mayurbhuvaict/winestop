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

use Exception;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Helper\Email;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Notification
 * @package Mageplaza\GiftCard\Cron
 */
class Notification
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * Notification constructor.
     *
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->_helper            = $helper;
        $this->_collectionFactory = $collectionFactory;
        $this->_logger            = $logger;
    }

    /**
     * Inform before GC expire after X day(s)
     */
    public function execute()
    {
        $this->notifyBeforeExpire()->notifyAfterUnused();
    }

    /**
     * @return $this
     */
    protected function notifyBeforeExpire()
    {
        $days = $this->getEmailDays(Email::EMAIL_TYPE_EXPIRE);
        if (!$days) {
            return $this;
        }

        $dateExpire = date('Y-m-d', strtotime("+{$days} day"));
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('status', Status::STATUS_ACTIVE)
            ->addFieldToFilter('expired_at', $dateExpire);

        /** @var GiftCard $giftCard */
        foreach ($collection as $giftCard) {
            try {
                $giftCard->sendToRecipient(Email::EMAIL_TYPE_EXPIRE, ['expire_days' => $days]);
            } catch (Exception $e) {
                $this->_logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function notifyAfterUnused()
    {
        $days = $this->getEmailDays(Email::EMAIL_TYPE_UNUSED);
        if (!$days || !$this->_helper->isEmailEnable(Email::EMAIL_TYPE_UNUSED)) {
            return $this;
        }

        $dateSent   = date('Y-m-d', strtotime("-{$days} day"));
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('status', Status::STATUS_ACTIVE)
            ->addFieldToFilter('delivery_date', ['notnull' => true])
            ->addFieldToFilter('delivery_date', $dateSent);
        $collection->getSelect()->where('balance = init_balance');

        /** @var GiftCard $giftCard */
        foreach ($collection as $giftCard) {
            try {
                $this->_helper->getEmailHelper()
                    ->sendNoticeSenderEmail($giftCard, Email::EMAIL_TYPE_UNUSED, ['unused_days' => $days]);
            } catch (Exception $e) {
                $this->_logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * @param $type
     *
     * @return int
     */
    private function getEmailDays($type)
    {
        return (int) $this->_helper->getEmailConfig($type . '/days');
    }
}

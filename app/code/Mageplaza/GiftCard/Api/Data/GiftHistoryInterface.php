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

namespace Mageplaza\GiftCard\Api\Data;

/**
 * Interface GiftHistoryInterface
 * @package Mageplaza\GiftCard\Api\Data
 */
interface GiftHistoryInterface
{
    const HISTORY_ID    = 'history_id';
    const GIFTCARD_ID   = 'giftcard_id';
    const CODE          = 'code';
    const ACTION        = 'action';
    const BALANCE       = 'balance';
    const AMOUNT        = 'amount';
    const STATUS        = 'status';
    const STORE_ID      = 'store_id';
    const EXTRA_CONTENT = 'extra_content';
    const CREATED_AT    = 'created_at';

    /**
     * @return int
     */
    public function getHistoryId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setHistoryId($value);

    /**
     * @return int
     */
    public function getGiftcardId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setGiftcardId($value);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCode($value);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAction($value);

    /**
     * @return float
     */
    public function getBalance();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setBalance($value);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setAmount($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @return string
     */
    public function getExtraContent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setExtraContent($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);
}

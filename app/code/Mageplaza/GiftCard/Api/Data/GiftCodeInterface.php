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
 * Interface GiftCodeInterface
 * @api
 */
interface GiftCodeInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const GIFTCARD_ID        = 'giftcard_id';
    const CODE               = 'code';
    const PATTERN            = 'pattern'; // yes
    const INIT_BALANCE       = 'init_balance';
    const BALANCE            = 'balance'; // yes
    const STATUS             = 'status'; // yes
    const CAN_REDEEM         = 'can_redeem'; // yes
    const STORE_ID           = 'store_id'; // yes
    const POOL_ID            = 'pool_id';
    const TEMPLATE_ID        = 'template_id'; // maybe
    const IMAGE              = 'image';
    const TEMPLATE_FIELDS    = 'template_fields'; // maybe
    const CUSTOMER_IDS       = 'customer_ids';
    const ORDER_ITEM_ID      = 'order_item_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const DELIVERY_METHOD    = 'delivery_method'; // maybe
    const DELIVERY_ADDRESS   = 'delivery_address'; // maybe
    const IS_SENT            = 'is_sent';
    const DELIVERY_DATE      = 'delivery_date'; // maybe
    const TIMEZONE           = 'timezone'; // maybe
    const EXTRA_CONTENT      = 'extra_content';
    const EXPIRED_AT         = 'expired_at'; // yes
    const CREATED_AT         = 'created_at';

    /**
     * @return string
     */
    public function getGiftcardId();

    /**
     * @param string $value
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
    public function getPattern();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPattern($value);

    /**
     * @return string
     */
    public function getInitBalance();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setInitBalance($value);

    /**
     * @return string
     */
    public function getBalance();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBalance($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getCanRedeem();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCanRedeem($value);

    /**
     * @return string
     */
    public function getStoreId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @return string
     */
    public function getPoolId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPoolId($value);

    /**
     * @return string
     */
    public function getTemplateId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTemplateId($value);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImage($value);

    /**
     * @return \Mageplaza\GiftCard\Api\Data\TemplateFieldsInterface|\Mageplaza\GiftCard\Model\Api\Data\TemplateFields
     */
    public function getTemplateFields();

    /**
     * @param \Mageplaza\GiftCard\Api\Data\TemplateFieldsInterface $value
     *
     * @return $this
     */
    public function setTemplateFields($value);

    /**
     * @return string
     */
    public function getCustomerIds();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerIds($value);

    /**
     * @return string
     */
    public function getOrderItemId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOrderItemId($value);

    /**
     * @return string
     */
    public function getOrderIncrementId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOrderIncrementId($value);

    /**
     * @return string
     */
    public function getDeliveryMethod();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDeliveryMethod($value);

    /**
     * @return string
     */
    public function getDeliveryAddress();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDeliveryAddress($value);

    /**
     * @return string
     */
    public function getIsSent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIsSent($value);

    /**
     * @return string
     */
    public function getDeliveryDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDeliveryDate($value);

    /**
     * @return string
     */
    public function getTimezone();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTimezone($value);

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
    public function getExpiredAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setExpiredAt($value);

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

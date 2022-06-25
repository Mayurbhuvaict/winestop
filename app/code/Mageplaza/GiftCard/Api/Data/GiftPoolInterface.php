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
 * Interface GiftPoolInterface
 * @api
 */
interface GiftPoolInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const POOL_ID         = 'pool_id';
    const NAME            = 'name'; // yes
    const STATUS          = 'status'; // yes
    const CAN_INHERIT     = 'can_inherit'; // yes
    const PATTERN         = 'pattern'; // yes
    const BALANCE         = 'balance'; // yes
    const CAN_REDEEM      = 'can_redeem'; // yes
    const STORE_ID        = 'store_id'; // yes
    const TEMPLATE_ID     = 'template_id'; // maybe
    const IMAGE           = 'image'; // maybe
    const TEMPLATE_FIELDS = 'template_fields'; // yes
    const EXPIRED_AT      = 'expired_at'; // yes
    const CREATED_AT      = 'created_at';

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
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

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
    public function getCanInherit();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCanInherit($value);

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
     * @return \Mageplaza\GiftCard\Api\Data\TemplateFieldsInterface
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

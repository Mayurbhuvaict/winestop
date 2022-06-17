<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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

namespace Mageplaza\GiftCard\Model\Api\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mageplaza\GiftCard\Api\Data\RedeemDetailInterface;

/**
 * Class RedeemDetail
 * @package Mageplaza\GiftCard\Model\Api\Data
 */
class RedeemDetail extends AbstractExtensibleModel implements RedeemDetailInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCustomerBalance()
    {
        return $this->getData(self::CUSTOMER_BALANCE);
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomerBalance($value)
    {
        return $this->setData(self::CUSTOMER_BALANCE, $value);
    }
}

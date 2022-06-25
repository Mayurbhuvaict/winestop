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

namespace Mageplaza\GiftCard\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface GuestGiftCardManagementInterface
 *
 * @package Mageplaza\GiftCard\Api
 */
interface GuestGiftCardManagementInterface
{
    /**
     * Add a gift card code to a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param string $code The gift card code data.
     *
     * @return bool
     * @throws NoSuchEntityException The specified cart does not exist.
     * @throws CouldNotSaveException The specified coupon could not be added.
     */
    public function set($cartId, $code);

    /**
     * Delete a gift card from a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param string $code The gift card code data.
     *
     * @return bool
     * @throws NoSuchEntityException The specified cart does not exist.
     * @throws CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function remove($cartId, $code);
}

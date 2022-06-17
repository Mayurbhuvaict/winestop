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

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface GiftHistoryManagementInterface
 * @package Mageplaza\GiftCard\Api
 */
interface GiftHistoryManagementInterface
{
    /**
     * @param string $id
     *
     * @return \Mageplaza\GiftCard\Api\Data\GiftHistoryInterface[]
     * @throws NoSuchEntityException
     */
    public function get($id);
}

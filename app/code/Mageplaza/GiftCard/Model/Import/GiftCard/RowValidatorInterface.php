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

namespace Mageplaza\GiftCard\Model\Import\GiftCard;

use Magento\Framework\Validator\ValidatorInterface;
use Mageplaza\GiftCard\Model\Import\GiftCard;

/**
 * Interface RowValidatorInterface
 * @package Mageplaza\GiftCard\Model\Import\GiftCard
 */
interface RowValidatorInterface extends ValidatorInterface
{
    const ERROR_CODE_IS_EMPTY    = 'codeIsEmpty';
    const ERROR_DUPLICATE_CODE   = 'duplicatedUrlKey';
    const ERROR_INVALID_TEMPLATE = 'invalidTemplate';
    const ERROR_INVALID_STATUS   = 'invalidStatus';
    const ERROR_INVALID_POOL     = 'invalidPool';
    const ERROR_INVALID_WEBSITE  = 'invalidWebsite';
    const ERROR_INVALID_BALANCE  = 'invalidBalance';
    const ERROR_INVALID_REDEEM   = 'invalidRedeem';
    const VALUE_ALL              = 'all'; #Value that means all entities (e.g. websites, groups etc.)

    /**
     * Initialize validator
     *
     * @param GiftCard $context
     *
     * @return $this
     */
    public function init($context);
}

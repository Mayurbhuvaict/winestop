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

namespace Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab;

use Mageplaza\GiftCard\Model\GiftCard;

/**
 * Class Condition
 * @package Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab
 */
class Condition extends \Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab\Condition
{
    /**
     * @return GiftCard
     */
    protected function getObject()
    {
        return $this->_coreRegistry->registry('current_giftcard');
    }
}

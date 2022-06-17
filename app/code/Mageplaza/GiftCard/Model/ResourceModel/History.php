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

namespace Mageplaza\GiftCard\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mageplaza\GiftCard\Model\GiftCard\Action;

/**
 * Class History
 * @package Mageplaza\GiftCard\Model\ResourceModel
 */
class History extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('mageplaza_giftcard_history', 'history_id');
    }

    /**
     * @param $giftCards
     * @param $extraContent
     *
     * @throws LocalizedException
     */
    public function createMultiple($giftCards, $extraContent)
    {
        $data = [];
        foreach ($giftCards as $card) {
            $data[] = [
                'action'        => Action::ACTION_CREATE,
                'giftcard_id'   => $card->getId(),
                'code'          => $card->getCode(),
                'balance'       => $card->getBalance(),
                'amount'        => $card->getBalance(),
                'status'        => $card->getStatus(),
                'extra_content' => $extraContent
            ];
        }

        $this->getConnection()->insertMultiple($this->getMainTable(), $data);
    }
}

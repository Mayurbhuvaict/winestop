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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Pool;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\GiftCard\Model\Pool;

/**
 * Class ManualAdd
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class ManualAdd extends Generate
{
    /**
     * @param Pool $pool
     *
     * @return array
     * @throws InputException
     * @throws LocalizedException
     */
    public function generate($pool)
    {
        $data = $this->getRequest()->getParams();

        if (!isset($data['codes'])) {
            throw new InputException(__('Invalid date provided'));
        }

        $codes = array_unique(explode(PHP_EOL, $data['codes']));

        return $this->generateByCodes($pool, $codes);
    }
}

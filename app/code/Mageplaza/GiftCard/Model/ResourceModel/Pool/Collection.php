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

namespace Mageplaza\GiftCard\Model\ResourceModel\Pool;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Mageplaza\GiftCard\Api\Data\GiftPoolSearchResultInterface;
use Mageplaza\GiftCard\Model\Pool;

/**
 * Class Collection
 * @package Mageplaza\GiftCard\Model\ResourceModel\Pool
 */
class Collection extends AbstractCollection implements GiftPoolSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'pool_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Pool::class, \Mageplaza\GiftCard\Model\ResourceModel\Pool::class);
    }
}

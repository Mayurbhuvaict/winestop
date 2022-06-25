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

namespace Mageplaza\GiftCard\Model\ResourceModel\Template;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Mageplaza\GiftCard\Api\Data\GiftTemplateSearchResultInterface;
use Mageplaza\GiftCard\Model\Template;

/**
 * Class Collection
 * @package Mageplaza\GiftCard\Model\ResourceModel\Template
 */
class Collection extends AbstractCollection implements GiftTemplateSearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'template_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Template::class, \Mageplaza\GiftCard\Model\ResourceModel\Template::class);
    }
}

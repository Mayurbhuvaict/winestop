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

namespace Mageplaza\GiftCard\Plugin\CustomerData\Checkout;

use Closure;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftCard\Helper\Media;

/**
 * Class Image
 * @package Mageplaza\GiftCard\Plugin\CustomerData\Checkout
 */
class Image
{
    /**
     * @var Media
     */
    private $mediaHelper;

    /**
     * Image constructor.
     *
     * @param Media $mediaHelper
     */
    public function __construct(Media $mediaHelper)
    {
        $this->mediaHelper = $mediaHelper;
    }

    /**
     * @param AbstractItem $subject
     * @param Closure $proceed
     * @param Item $item
     *
     * @return array
     */
    public function aroundGetItemData(AbstractItem $subject, Closure $proceed, Item $item)
    {
        $result = $proceed($item);

        $image = $item->getOptionByCode('image');

        if (!$image || empty($result['product_image'])) {
            return $result;
        }

        if ($url = $this->mediaHelper->getGiftCardImageProduct($item, $image->getValue())) {
            $result['product_image']['src']    = $url;
            $result['product_image']['height'] = null;
        }

        return $result;
    }
}

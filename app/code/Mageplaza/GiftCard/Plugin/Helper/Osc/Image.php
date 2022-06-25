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

namespace Mageplaza\GiftCard\Plugin\Helper\Osc;

use Closure;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftCard\Helper\Media;

/**
 * Class Image
 * @package Mageplaza\GiftCard\Plugin\Helper\Osc
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
     * @param \Mageplaza\Osc\Helper\Item $subject
     * @param Closure $proceed
     * @param Item $item
     *
     * @return array
     */
    public function aroundGetItemImages(\Mageplaza\Osc\Helper\Item $subject, Closure $proceed, Item $item)
    {
        $result = $proceed($item);

        $image = $item->getOptionByCode('image');

        if (!$image) {
            return $result;
        }

        if ($url = $this->mediaHelper->getGiftCardImageProduct($item, $image->getValue())) {
            $result['src']    = $url;
            $result['height'] = null;
        }

        return $result;
    }
}

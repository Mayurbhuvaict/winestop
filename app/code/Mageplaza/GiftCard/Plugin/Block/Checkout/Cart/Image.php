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

namespace Mageplaza\GiftCard\Plugin\Block\Checkout\Cart;

use Magento\Checkout\Block\Cart\Item\Renderer;
use Mageplaza\GiftCard\Helper\Media;

/**
 * Class Image
 * @package Mageplaza\GiftCard\Plugin\Block\Checkout\Cart
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
     * @param Renderer $subject
     * @param \Magento\Catalog\Block\Product\Image $result
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function afterGetImage(Renderer $subject, $result)
    {
        $item = $subject->getItem();

        $image = $item->getOptionByCode('image');

        if (!$image) {
            return $result;
        }

        if ($url = $this->mediaHelper->getGiftCardImageProduct($item, $image->getValue())) {
            $result->setImageUrl($url);
        }

        return $result;
    }
}

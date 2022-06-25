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

namespace Mageplaza\GiftCard\Plugin\Block\Catalog;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Image;
use Magento\Wishlist\Model\Item;
use Mageplaza\GiftCard\Helper\Media;

/**
 * Class ProductImage
 * @package Mageplaza\GiftCard\Plugin\Block\Catalog
 */
class ProductImage
{
    /**
     * @var Media
     */
    private $mediaHelper;

    /**
     * ProductImage constructor.
     *
     * @param Media $mediaHelper
     */
    public function __construct(Media $mediaHelper)
    {
        $this->mediaHelper = $mediaHelper;
    }

    /**
     * @param AbstractProduct $subject
     * @param Image $result
     *
     * @return Image
     */
    public function afterGetImage(AbstractProduct $subject, $result)
    {
        /** @var Item $item */
        $item = $subject->getItem();

        if (!$item) {
            return $result;
        }

        $image = $item->getBuyRequest()['image'] ?? '';

        if ($url = $this->mediaHelper->getGiftCardImageProduct($item, $image)) {
            $result->setImageUrl($url);
        }

        return $result;
    }
}

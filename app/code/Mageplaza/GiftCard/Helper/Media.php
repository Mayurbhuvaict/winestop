<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\GiftCard\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;

/**
 * Class Media
 * @package Mageplaza\GiftCard\Helper
 */
class Media extends \Mageplaza\Core\Helper\Media
{
    const TEMPLATE_MEDIA_PATH = 'mageplaza/giftcard';

    /**
     * @var string
     */
    protected $placeHolderImage;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * Media constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param AdapterFactory $imageFactory
     * @param Repository $assetRepo
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        AdapterFactory $imageFactory,
        Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;

        parent::__construct($context, $objectManager, $storeManager, $filesystem, $uploaderFactory, $imageFactory);
    }

    /**
     * @return string
     */
    public function getPlaceHolderImage()
    {
        if ($this->placeHolderImage === null) {
            $this->placeHolderImage = $this->assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');
        }

        return $this->placeHolderImage;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return parent::getBaseMediaUrl() . '/' . self::TEMPLATE_MEDIA_PATH;
    }

    /**
     * @param $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . '/tmp/' . $this->_prepareFile($file);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isShowGiftcardImageProduct($storeId = null)
    {
        return (bool) $this->getConfigValue('mpgiftcard/checkout/show_giftcard_image_product', $storeId);
    }

    /**
     * @param Item|AbstractItem|\Magento\Wishlist\Model\Item $item
     * @param string $image
     *
     * @return string
     */
    public function getGiftCardImageProduct($item, $image)
    {
        try {
            if (!$image
                || $item->getProduct()->getTypeId() !== GiftCard::TYPE_GIFTCARD
                || !$this->isShowGiftcardImageProduct($item->getStoreId())) {
                return null;
            }
        } catch (LocalizedException $e) {
            return null;
        }

        $pos = strpos($image, '.tmp');

        try {
            if ($pos !== false) {
                return $this->getTmpMediaUrl(substr($image, 0, $pos));
            }

            return $this->getMediaUrl($image);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}

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

namespace Mageplaza\GiftCard\Plugin\Quote;

use Closure;
use Exception;
use Magento\Catalog\Model\Product\Attribute\Source\Boolean;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Helper\Product;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\Source\FieldRenderer;
use Psr\Log\LoggerInterface;

/**
 * Class ToOrderItem
 * @package Mageplaza\GiftCard\Plugin\Quote
 */
class ToOrderItem
{
    /**
     * @var Data
     */
    private $_dataHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ToOrderItem constructor.
     *
     * @param Data $dataHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $dataHelper,
        LoggerInterface $logger
    ) {
        $this->_dataHelper = $dataHelper;
        $this->logger      = $logger;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param Closure $proceed
     * @param AbstractItem $item
     * @param array $data
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        AbstractItem $item,
        $data = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $data);

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $item->getProduct();

        $productOptions = $orderItem->getProductOptions();
        foreach (FieldRenderer::getFullOptionArray() as $key => $label) {
            $option = $product->getCustomOption($key);
            if ($option) {
                $productOptions[$key] = $option->getValue();
            }
        }

        $canRedeem = (int) $product->getCanRedeem();
        if ($canRedeem === Boolean::VALUE_USE_CONFIG) {
            $canRedeem = $this->_dataHelper->getGeneralConfig('can_redeem', $orderItem->getStore());
        }
        $productOptions['can_redeem'] = $canRedeem;

        $expiredDay = $product->getExpireAfterDay();
        if ($expiredDay === Product::VALUE_USE_CONFIG) {
            $expiredDay = $this->_dataHelper->getProductConfig('expire_after_day', $orderItem->getStore());
        }
        $productOptions['expire_after'] = $expiredDay;

        $pattern = $product->getGiftCodePattern();
        if ($pattern === Product::VALUE_USE_CONFIG) {
            $pattern = $this->_dataHelper->getGeneralConfig('pattern', $orderItem->getStore());
        }
        $productOptions['pattern'] = $pattern;

        $productOptions['giftcard_type'] = $product->getGiftCardType();

        //Copy images uploaded
        if (isset($productOptions['image'])) {
            $image = $productOptions['image'];

            $pos = strpos($image, '.tmp');
            if ($pos !== false) {
                unset($productOptions['image']);

                /** @var Template $templateHelper */
                $templateHelper = $this->_dataHelper->getTemplateHelper();

                /** @var WriteInterface $mediaDirectory */
                $mediaDirectory = $templateHelper->getMediaDirectory();

                $fileName = substr($image, 0, $pos);
                $filePath = $templateHelper->getTmpMediaPath($fileName);
                $file     = $mediaDirectory->getRelativePath($filePath);
                if ($mediaDirectory->isFile($file)) {
                    $pathInfo       = pathinfo($file);
                    $fileName       = Uploader::getCorrectFileName($pathInfo['basename']);
                    $dispretionPath = Uploader::getDispretionPath($fileName);
                    $fileName       = $dispretionPath . '/' . $fileName;

                    $fileName        = $templateHelper->getNotDuplicatedFilename($fileName, $dispretionPath);
                    $destinationFile = $templateHelper->getMediaPath($fileName);

                    try {
                        $mediaDirectory->renameFile($file, $destinationFile);
                        $productOptions['image'] = str_replace('\\', '/', $fileName);
                    } catch (Exception $e) {
                        $this->logger->critical($e->getMessage());
                    }
                }
            }
        }

        $orderItem->setProductOptions($productOptions);

        return $orderItem;
    }
}

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

namespace Mageplaza\GiftCard\Model\Product\Type;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Mageplaza\GiftCard\Helper\Product as DataHelper;
use Mageplaza\GiftCard\Model\Product\DeliveryMethods;
use Mageplaza\GiftCard\Model\Source\FieldRenderer;
use Psr\Log\LoggerInterface;
use Zend_Serializer_Exception;

/**
 * Class GiftCard
 * @package Mageplaza\GiftCard\Model\Product\Type
 */
class GiftCard extends AbstractType
{
    const TYPE_GIFTCARD = 'mpgiftcard';

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * If product can be configured
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DeliveryMethods
     */
    private $deliveryMethods;

    /**
     * GiftCard constructor.
     *
     * @param Option $catalogProductOption
     * @param Config $eavConfig
     * @param Type $catalogProductType
     * @param ManagerInterface $eventManager
     * @param Database $fileStorageDb
     * @param Filesystem $filesystem
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param DataHelper $dataHelper
     * @param Http $request
     * @param DeliveryMethods $deliveryMethods
     */
    public function __construct(
        Option $catalogProductOption,
        Config $eavConfig,
        Type $catalogProductType,
        ManagerInterface $eventManager,
        Database $fileStorageDb,
        Filesystem $filesystem,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        DataHelper $dataHelper,
        Http $request,
        DeliveryMethods $deliveryMethods
    ) {
        $this->request         = $request;
        $this->_dataHelper     = $dataHelper;
        $this->logger          = $logger;
        $this->deliveryMethods = $deliveryMethods;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * Sets flag that product has required options, because gift card always
     * has some required options, at least - recipient name
     *
     * @param Product $product
     *
     * @return $this
     */
    public function beforeSave($product)
    {
        parent::beforeSave($product);

        $product->setTypeHasOptions(true);
        $product->setTypeHasRequiredOptions(true);

        return $this;
    }

    /**
     * Check is virtual product
     *
     * @param Product|AbstractModel $product
     *
     * @return bool
     */
    public function isVirtual($product)
    {
        $product = $product->load($product->getId());

        return (int) $product->getGiftCardType() !== DeliveryMethods::TYPE_MAIL;
    }

    /**
     * Check if Gift Card product is available for sale
     *
     * @param Product|AbstractModel $product
     *
     * @return bool
     */
    public function isSalable($product)
    {
        $product = $product->load($product->getId());

        if (!$product->getAllowAmountRange() && !count($product->getGiftCardAmounts())) {
            return false;
        }

        return parent::isSalable($product);
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string $processMode
     *
     * @return array|Phrase|string
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($result)) {
            return $result;
        }

        try {
            return $this->prepareGiftCardData($buyRequest, $product->load($product->getId()));
        } catch (LocalizedException $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            $this->logger->critical($e);

            return __('Something went wrong.');
        }
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     *
     * @return array
     * @throws LocalizedException
     */
    protected function prepareGiftCardData($buyRequest, $product)
    {
        $redirectUrl = $this->request->getServer('REDIRECT_URL');
        if (strpos($redirectUrl, 'wishlist/index/add/') !== false) {
            return [$product];
        }

        $amount = $this->_validateAmount($buyRequest, $product);
        $product->addCustomOption(FieldRenderer::AMOUNT, $amount, $product);

        $deliveryOptions = $this->deliveryMethods->validateMethodFields((int) $buyRequest->getDelivery(), $buyRequest);
        foreach ($deliveryOptions as $key => $option) {
            $product->addCustomOption($key, $option, $product);
        }

        if ($sender = $buyRequest->getFrom()) {
            $product->addCustomOption(FieldRenderer::SENDER, ucfirst($sender), $product);
        }
        if ($recipient = $buyRequest->getTo()) {
            $product->addCustomOption(FieldRenderer::RECIPIENT, ucfirst($recipient), $product);
        }
        if ($template = $buyRequest->getTemplate()) {
            $product->addCustomOption(FieldRenderer::TEMPLATE, $template, $product);
        }
        if ($image = $buyRequest->getImage()) {
            $templateHelper = $this->_dataHelper->getTemplateHelper();
            $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

            $pos      = strpos($image, '.tmp');
            $filePath = ($pos === false)
                ? $templateHelper->getMediaPath($image)
                : $templateHelper->getTmpMediaPath(substr($image, 0, $pos));

            $file        = $mediaDirectory->getRelativePath($filePath);
            $reOrderFile = $mediaDirectory->getRelativePath($templateHelper->getMediaPath(substr($image, 0, $pos)));

            if ($mediaDirectory->isFile($file)) {
                $product->addCustomOption(FieldRenderer::IMAGE, $image, $product);
            } elseif ($mediaDirectory->isFile($reOrderFile)) {
                $product->addCustomOption(FieldRenderer::IMAGE, substr($image, 0, $pos), $product);
            }
        }
        if ($message = $buyRequest->getMessage()) {
            $product->addCustomOption(FieldRenderer::MESSAGE, $message, $product);
        }

        return [$product];
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     *
     * @return float
     * @throws LocalizedException
     */
    protected function _validateAmount($buyRequest, $product)
    {
        $isRange       = $buyRequest->getRangeAmount();
        $amount        = $buyRequest->getAmount();
        $currentAction = $this->request->getFullActionName();

        if ($isRange) {
            if ($product->getAllowAmountRange()) {
                $minAmount = $product->getMinAmount();
                $maxAmount = $product->getMaxAmount();
                if ($amount <= 0
                    || ($minAmount && ($amount < $minAmount))
                    || ($maxAmount && ($amount > $maxAmount))
                ) {
                    throw new LocalizedException(__('Range amount is incorrect, please choose your range amount again'));
                }
            } else {
                throw new LocalizedException(__('Range amount is not allowed'));
            }
            $product->addCustomOption('range_amount', true, $product);
        } else {
            $allowAmounts = [];
            $attribute    = $product->getResource()->getAttribute('gift_card_amounts');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($product);
                $allowAmounts = $product->getGiftCardAmounts();
            }

            if ($currentAction !== 'wishlist_index_add' && (!count($allowAmounts)
                    || !in_array($amount, array_column($allowAmounts, 'amount'), true))) {
                throw new LocalizedException(__('Please choose your amount again'));
            }

            $product->addCustomOption('range_amount', false, $product);
        }

        return $amount;
    }

    /**
     * Check if product can be bought
     *
     * @param Product $product
     *
     * @return $this
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
            $buyRequest = new DataObject($this->_dataHelper->unserialize($option->getValue()));
            $this->prepareGiftCardData($buyRequest, $product);
        }

        return $this;
    }

    /**
     * @param Product $product
     * @param DataObject $buyRequest
     *
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $delivery = (int) $buyRequest->getDelivery();
        $options  = [
            'amount'        => $buyRequest->getAmount(),
            'range_amount'  => (boolean) $buyRequest->getRangeAmount(),
            'template'      => $buyRequest->getTemplate(),
            'image'         => $buyRequest->getImage(),
            'from'          => $buyRequest->getFrom(),
            'to'            => $buyRequest->getTo(),
            'message'       => $buyRequest->getMessage(),
            'delivery_date' => $buyRequest->getDeliveryDate(),
            'timezone'      => $buyRequest->getTimezone(),
            'delivery'      => $delivery,
        ];

        $image = $buyRequest->getImage();
        if ($image) {
            $options['image'] = $image;
            $pos              = strpos($image, '.tmp');
            if ($pos !== false) {
                $imageTmp       = substr($image, 0, $pos);
                $templateHelper = $this->_dataHelper->getTemplateHelper();

                $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                if ($mediaDirectory->isFile($templateHelper->getTmpMediaPath($imageTmp))) {
                    $options['imageSrc'] = $templateHelper->getTmpMediaUrl($imageTmp);
                } elseif ($mediaDirectory->isFile($templateHelper->getMediaPath($imageTmp))) {
                    $options['imageSrc'] = $templateHelper->getMediaUrl($imageTmp);
                }
            }
        }

        if ($delivery === DeliveryMethods::METHOD_EMAIL) {
            $options['email'] = $buyRequest->getEmail();
        } elseif ($delivery === DeliveryMethods::METHOD_SMS) {
            $options['phone_number'] = $buyRequest->getPhoneNumber();
        }

        return $options;
    }

    /**
     * Delete data specific for Gift Card product type
     *
     * @param Product $product
     *
     * @return void
     */
    public function deleteTypeSpecificData(Product $product)
    {
    }
}

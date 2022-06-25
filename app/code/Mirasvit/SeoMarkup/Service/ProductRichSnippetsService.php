<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoMarkup\Service;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoMarkup\Block\Rs\Product\AggregateOfferData;
use Mirasvit\SeoMarkup\Block\Rs\Product\OfferData;
use Mirasvit\SeoMarkup\Block\Rs\Product\RatingData;
use Mirasvit\SeoMarkup\Block\Rs\Product\ReviewData;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class ProductRichSnippetsService extends Template
{
    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var ProductConfig
     */
    private $productConfig;

    /**
     * @var TemplateEngineServiceInterface
     */
    private $templateEngineService;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var OfferData
     */
    private $offerData;

    /**
     * @var AggregateOfferData
     */
    private $aggregateOfferData;

    /**
     * @var ReviewData
     */
    private $reviewData;

    /**
     * @var RatingData
     */
    private $ratingData;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * ProductRichSnippetsService constructor.
     *
     * @param ProductConfig                  $productConfig
     * @param TemplateEngineServiceInterface $templateEngineService
     * @param OfferData                      $offerData
     * @param AggregateOfferData             $aggregateOfferData
     * @param ReviewData                     $reviewData
     * @param RatingData                     $ratingData
     * @param ImageHelper                    $imageHelper
     * @param Registry                       $registry
     * @param Context                        $context
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        ProductConfig $productConfig,
        TemplateEngineServiceInterface $templateEngineService,
        OfferData $offerData,
        AggregateOfferData $aggregateOfferData,
        ReviewData $reviewData,
        RatingData $ratingData,
        ImageHelper $imageHelper,
        Registry $registry,
        Context $context
    ) {
        $this->productConfig         = $productConfig;
        $this->templateEngineService = $templateEngineService;
        $this->offerData             = $offerData;
        $this->aggregateOfferData    = $aggregateOfferData;
        $this->reviewData            = $reviewData;
        $this->ratingData            = $ratingData;
        $this->imageHelper           = $imageHelper;
        $this->store                 = $context->getStoreManager()->getStore();
        $this->registry              = $registry;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param bool                           $dry
     *
     * @return array|bool
     */
    public function getJsonData($product, $dry = false)
    {
        if (!$product) {
            return false;
        }

        $product = $dry ? $product : $product->load($product->getId());

        if ($dry === false && in_array($product->getTypeId(), ['configurable', 'bundle', 'grouped'])) {
            $offer = $this->aggregateOfferData->getData($product, $this->store);
        } else {
            $offer = $this->offerData->getData($product, $this->store, $dry);
        }

        $values = [
            '@context'        => 'http://schema.org',
            '@type'           => 'Product',
            'name'            => $this->templateEngineService->render('[product_name]', ['product' => $product]),
            'sku'             => $this->templateEngineService->render('[product_sku]', ['product' => $product]),
            'mpn'             => $this->getManufacturerPartNumber($product),
            'image'           => $this->getImage($product),
            'category'        => $this->getCategoryName($product),
            'brand'           => $this->getBrand($product),
            'model'           => $this->getModel($product),
            'color'           => $this->getColor($product),
            'weight'          => $this->getWeight($product),
            'width'           => $this->getDimensionValue('width', $product),
            'height'          => $this->getDimensionValue('height', $product),
            'depth'           => $this->getDimensionValue('depth', $product),
            'description'     => $this->getDescription($product),
            'gtin8'           => $this->getGtinValue(8, $product),
            'gtin12'          => $this->getGtinValue(12, $product),
            'gtin13'          => $this->getGtinValue(13, $product),
            'gtin14'          => $this->getGtinValue(14, $product),
            'offers'          => $offer,
            'review'          => $this->reviewData->getData($product, $this->store),
            'aggregateRating' => $this->ratingData->getData($product, $this->store),
        ];

        $data = [];

        foreach ($values as $key => $value) {
            if ($value) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string|false
     */
    private function getManufacturerPartNumber($product)
    {
        if ($this->productConfig->isMpnEnabled() && $attribute = $this->productConfig->getManufacturerPartNumber()) {
            return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string|false
     */
    private function getImage($product)
    {
        if ($this->productConfig->isImageEnabled()) {
            return $this->imageHelper->init($product, 'product_page_image_large')->getUrl();
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string|false
     */
    private function getCategoryName($product)
    {
        if (!$this->productConfig->isCategoryEnabled()) {
            return false;
        }

        return $this->templateEngineService->render('[product_category_name]', ['product' => $product]);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array|false
     */
    private function getBrand($product)
    {
        if ($attribute = $this->productConfig->getBrandAttribute()) {
            if ($brand = $this->templateEngineService->render("[product_$attribute]", ['product' => $product])) {
                return [
                    '@type' => 'Brand',
                    'name'  => $brand
                ];
            }
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string|false
     */
    private function getModel($product)
    {
        if ($attribute = $this->productConfig->getModelAttribute()) {
            return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string|false
     */
    private function getColor($product)
    {
        if ($attribute = $this->productConfig->getColorAttribute()) {
            return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
        }

        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array|false
     */
    private function getWeight($product)
    {
        $unitCode = $this->productConfig->getWeightUnitType();

        if (!$unitCode) {
            return false;
        }

        $value = $this->templateEngineService->render('[product_weight]', ['product' => $product]);

        if (!$value) {
            return false;
        }
        $value = number_format((float)$value, 4);

        return [
            '@type'    => 'QuantitativeValue',
            'value'    => $value,
            'unitCode' => $unitCode,
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return false|string
     */
    private function getDescription($product)
    {
        $value          = false;
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $contentService = $objectManager->get('Mirasvit\SeoContent\Service\ContentService');
        $content        = $contentService->getCurrentContent(TemplateInterface::RULE_TYPE_PRODUCT, $product);

        switch ($this->productConfig->getDescriptionType()) {
            case ProductConfig::DESCRIPTION_TYPE_DESCRIPTION:
                $description = $content->getData('full_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[product_description]', ['product' => $product]);
                break;
            case ProductConfig::DESCRIPTION_TYPE_META:
                $description = $content->getData('meta_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[page_meta_description]', ['product' => $product]);
                break;
            case ProductConfig::DESCRIPTION_TYPE_SHORT_DESCRIPTION:
                $description = $content->getData('short_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[product_short_description]', ['product' => $product]);
                break;
            default:
                $value = $product->getShortDescription();
                break;
        }

        return $value ? strip_tags($value) : false;
    }

    /**
     * @param string                         $type
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array|false
     */
    private function getDimensionValue($type, $product)
    {
        if (!$this->productConfig->isDimensionsEnabled()) {
            return false;
        }

        $unitCode = $this->productConfig->getDimensionUnit();

        if (!$unitCode) {
            return false;
        }

        switch ($type) {
            case 'width':
                $attribute = $this->productConfig->getDimensionWidthAttribute();
                break;

            case 'height':
                $attribute = $this->productConfig->getDimensionHeightAttribute();
                break;

            case 'depth':
                $attribute = $this->productConfig->getDimensionDepthAttribute();
                break;

            default:
                $attribute = false;
        }

        if (!$attribute) {
            return false;
        }

        $value = $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);

        if (!$value) {
            return false;
        }

        return [
            '@type'    => 'QuantitativeValue',
            'value'    => $value,
            'unitCode' => $unitCode,
        ];
    }

    /**
     * @param int                            $number
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return false|string
     */
    private function getGtinValue($number, $product)
    {
        switch ($number) {
            case 8:
                $attribute = $this->productConfig->getGtin8Attribute();
                break;

            case 12:
                $attribute = $this->productConfig->getGtin12Attribute();
                break;

            case 13:
                $attribute = $this->productConfig->getGtin13Attribute();
                break;

            case 14:
                $attribute = $this->productConfig->getGtin14Attribute();
                break;

            default:
                $attribute = false;
        }

        if (!$attribute) {
            return false;
        }

        return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
    }
}

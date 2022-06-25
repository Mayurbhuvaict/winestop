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



namespace Mirasvit\SeoMarkup\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ProductConfig
{
    const DESCRIPTION_TYPE_DESCRIPTION       = 1;
    const DESCRIPTION_TYPE_META              = 2;
    const DESCRIPTION_TYPE_SHORT_DESCRIPTION = 3;

    const WEIGHT_UNIT_KG = 'KGM';
    const WEIGHT_UNIT_LB = 'LBR';
    const WEIGHT_UNIT_G  = 'GRM';

    const ITEM_CONDITION_MANUAL  = 1;
    const ITEM_CONDITION_NEW_ALL = 2;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function isRsEnabled($store)
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isRemoveNativeRs()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_remove_native_rs');
    }

    /**
     * @return int
     */
    public function getDescriptionType()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/description_type');
    }

    /**
     * @return bool
     */
    public function isImageEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_image_enabled');
    }

    /**
     * @return bool
     */
    public function isAvailabilityEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_availability_enabled');
    }

    /**
     * @return bool
     */
    public function isAcceptedPaymentMethodEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_accepted_payment_method_enabled');
    }

    /**
     * @return bool
     */
    public function isAvailableDeliveryMethodEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_available_delivery_method_enabled');
    }

    /**
     * @return bool
     */
    public function isCategoryEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_category_enabled');
    }

    /**
     * @return bool
     */
    public function isMpnEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_mpn_enabled');
    }

    /**
     * @return string
     */
    public function getBrandAttribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/brand_attribute');
    }

    /**
     * @return string
     */
    public function getModelAttribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/model_attribute');
    }

    /**
     * @return string
     */
    public function getManufacturerPartNumber()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/mpn_attribute');
    }

    /**
     * @return string
     */
    public function getColorAttribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/color_attribute');
    }

    /**
     * @return string
     */
    public function getWeightUnitType()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/weight_unit_type');
    }

    /**
     * @return bool
     */
    public function isDimensionsEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_dimensions_enabled');
    }

    /**
     * @return string
     */
    public function getDimensionUnit()
    {
        return trim((string)$this->scopeConfig->getValue('seo/seo_markup/product/dimension_unit'));
    }

    /**
     * @return string
     */
    public function getDimensionHeightAttribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/dimension_height_attribute');
    }

    /**
     * @return string
     */
    public function getDimensionWidthAttribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/dimension_width_attribute');
    }

    /**
     * @return string
     */
    public function getDimensionDepthAttribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/dimension_depth_attribute');
    }

    /**
     * @return string
     */
    public function getItemConditionType()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/item_condition_type');
    }

    /**
     * @return string
     */
    public function getItemConditionAttribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/item_condition_attribute');
    }

    /**
     * @return string
     */
    public function getItemConditionAttributeValueNew()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/item_condition_attribute_value_new');
    }

    /**
     * @return string
     */
    public function getItemConditionAttributeValueUsed()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/item_condition_attribute_value_used');
    }

    /**
     * @return string
     */
    public function getItemConditionAttributeValueRefurbished()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/item_condition_attribute_value_refurbished');
    }

    /**
     * @return string
     */
    public function getItemConditionAttributeValueDamaged()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/item_condition_attribute_value_damaged');
    }

    /**
     * @return bool
     */
    public function isIndividualReviewsEnabled()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/is_individual_reviews_enabled');
    }

    /**
     * @return string
     */
    public function getGtin8Attribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/gtin8_attribute');
    }

    /**
     * @return string
     */
    public function getGtin12Attribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/gtin12_attribute');
    }

    /**
     * @return string
     */
    public function getGtin13Attribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/gtin13_attribute');
    }

    /**
     * @return string
     */
    public function getGtin14Attribute()
    {
        return $this->scopeConfig->getValue('seo/seo_markup/product/gtin14_attribute');
    }
}

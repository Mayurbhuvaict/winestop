<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Helper;

/**
 * Attributes management
 */
class AttributesPrices extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\RuleFactory|null
     */
    protected $ruleFactory = null;
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory|null
     */
    protected $salesRuleCollectionFactory = null;
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Framework\App\Helper\Context $context, \Magento\CatalogRule\Model\ResourceModel\RuleFactory $ruleFactory, \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $salesRuleCollectionFactory)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->ruleFactory = $ruleFactory;
        $this->salesRuleCollectionFactory = $salesRuleCollectionFactory;
        parent::__construct($context);
    }
    public function validateCond($conditions, $item)
    {
        if ($item->getProductId() == '') {
            $item->setProductId($item->getId());
        }
        if ($item->getProductId() != '' && $item->getProduct() == '') {
            $item->setProduct($this->productRepository->getById($item->getProductId()));
        }
        $all = $conditions->getAggregator() === 'all';
        $true = (bool) $conditions->getValue();
        $found = $all;
        foreach ($conditions->getConditions() as $condition) {
            $validated = $condition->validate($item);
            if ($all && !$validated || !$all && $validated) {
                $found = $validated;
                break;
            }
        }
        if ($found && $true) {
            return true;
        } elseif (!$found && !$true) {
            return true;
        }
        return false;
    }
    public function promotionId($model, $options, $product, $reference)
    {
        if (!$this->moduleHelper->moduleIsEnabled('Wyomind_GoogleMerchantPromotions')) {
            return '';
        }
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $notProceeded = ['Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Subselect', 'Magento\\SalesRule\\Model\\Rule\\Condition\\Address'];
        $rules = $this->salesRuleCollectionFactory->create();
        $rules->addFieldToFilter('transferable_to_google_merchant', 1);
        $rules->addFieldToFilter('is_active', 1);
        foreach ($rules as $rule) {
            if ($rule->getIsActive()) {
                $conditions = $rule->getConditions();
                $all = $conditions->getAggregator() === 'all';
                $true = (bool) $conditions->getValue();
                $rtnCond = $all ? true : false;
                $rtnCond = !count($conditions->getConditions()) ? true : $rtnCond;
                foreach ($conditions->getConditions() as $condition) {
                    if (!in_array($condition->getType(), $notProceeded)) {
                        if ($condition->getType() == 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Found') {
                            $validated = $this->validateCond($condition, $item);
                        } else {
                            $validated = $condition->validate($item);
                        }
                        if ($all && $validated !== $true) {
                            $rtnCond = false;
                        } elseif (!$all && $validated === $true) {
                            $rtnCond = true;
                            break;
                        }
                    } else {
                        $rtnCond = false;
                    }
                }
                $actions = $rule->getActions();
                $all = $actions->getAggregator() === 'all';
                $true = (bool) $actions->getValue();
                $rtnAct = $all ? true : false;
                $rtnAct = !count($actions->getConditions()) ? true : $rtnAct;
                foreach ($actions->getConditions() as $act) {
                    $validated = $act->validate($item);
                    if ($all && $validated !== $true) {
                        $rtnAct = false;
                    } elseif (!$all && $validated === $true) {
                        $rtnAct = true;
                        break;
                    }
                }
                if ($rtnAct && $rtnCond) {
                    return $rule->getData('rule_id');
                }
            }
        }
        return '';
    }
    /**
     * {g_sale_price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string g:sale_price + g:sale_price_effective_date xml tags
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function price($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $timestamp = $this->localeDate->scopeDate($model->params['store_id']);
        $websiteId = $model->storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $rulePrice = $this->ruleFactory->create()->getRulePrice($timestamp, $websiteId, $customerGroupId, $item->getId());
        if ($rulePrice !== false) {
            $priceRules = sprintf('%.2f', round($rulePrice, 2));
        } else {
            $priceRules = $item->getPrice();
        }
        // "From date" defined but not "To date"
        if ($item->getSpecialFromDate() && !$item->getSpecialToDate()) {
            // Valid promotion date
            if ($item->getSpecialFromDate() <= $this->coreDate->date('Y-m-d H:i:s')) {
                if ($item->getTypeID() == 'bundle') {
                    if (($item->getPriceType() || !$item->getPriceType() && $item->getSpecialPrice() < $item->getPrice()) && $item->getSpecialPrice() > 0) {
                        if ($item->getPriceType()) {
                            $price = $item->getPrice() * $item->getSpecialPrice() / 100;
                        } else {
                            $price = $item->getSpecialPrice();
                        }
                    } else {
                        $price = $item->getPrice();
                    }
                } else {
                    // If special price exists
                    $price = $item->getSpecialPrice() && $item->getSpecialPrice() < $item->getPrice() ? $item->getSpecialPrice() : $priceRules;
                }
            } else {
                // Display regular price
                if ($item->getTypeID() == 'bundle') {
                    $price = $item->getPrice();
                } else {
                    $price = $priceRules;
                }
            }
        } elseif ($item->getSpecialFromDate() && $item->getSpecialToDate()) {
            // "From date" and "To date" defined
            //Valid promotion date
            if ($item->getSpecialFromDate() <= $this->coreDate->date('Y-m-d H:i:s') && $this->coreDate->date('Y-m-d H:i:s') < $item->getSpecialToDate()) {
                if ($item->getTypeID() == 'bundle') {
                    if (($item->getPriceType() || !$item->getPriceType() && $item->getSpecialPrice() < $item->getPrice()) && $item->getSpecialPrice() > 0) {
                        if ($item->getPriceType()) {
                            $price = $item->getPrice() * $item->getSpecialPrice() / 100;
                        } else {
                            $price = $item->getSpecialPrice();
                        }
                    } else {
                        $price = $item->getPrice();
                    }
                } else {
                    // If special price exists
                    $price = $item->getSpecialPrice() && $item->getSpecialPrice() < $item->getPrice() ? $item->getSpecialPrice() : $priceRules;
                }
            } else {
                // Display regular price
                if ($item->getTypeID() == 'bundle') {
                    $price = $item->getPrice();
                } else {
                    $price = $priceRules;
                }
            }
        } else {
            if ($item->getTypeID() == 'bundle') {
                if (($item->getPriceType() || !$item->getPriceType() && $item->getSpecialPrice() < $item->getPrice()) && $item->getSpecialPrice() > 0) {
                    if ($item->getPriceType()) {
                        $price = number_format($item->getPrice() * $item->getSpecialPrice() / 100, 2, ".", "");
                    } else {
                        $price = $item->getSpecialPrice();
                    }
                } else {
                    $price = $item->getPrice();
                }
            } else {
                // If special price exists
                $price = $item->getSpecialPrice() && $item->getSpecialPrice() < $item->getPrice() ? $item->getSpecialPrice() : $priceRules;
            }
        }
        if ($priceRules !== false) {
            if ($priceRules < $price) {
                $value = $priceRules;
            } else {
                $value = $price;
            }
        } else {
            $value = $price;
        }
        $value = $this->applyTaxThenCurrency($model, $item->getTaxClassId(), number_format($value, 2, '.', ''), $options, $reference);
        if ($value <= 0) {
            return null;
        }
        return $value;
    }
    public function salePriceEffectiveDate($model, $options, $product, $reference)
    {
        unset($options);
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $offsetHours = $this->coreDate->getGmtOffset('hours');
        if ($offsetHours > 0) {
            $sign = '+';
            $offset = str_pad(abs(floor($offsetHours)), 2, 0, STR_PAD_LEFT) . '' . str_pad((abs($offsetHours) - floor(abs($offsetHours))) * 60, 2, 0, STR_PAD_LEFT);
        } else {
            $sign = '-';
            $offset = str_pad(abs(floor($offsetHours)), 2, 0, STR_PAD_LEFT) . '' . str_pad((abs($offsetHours) - floor(abs($offsetHours))) * 60, 2, 0, STR_PAD_LEFT);
        }
        $from = substr(str_replace(' ', 'T', $item->getSpecialFromDate()), 0, -3);
        $to = substr(str_replace(' ', 'T', $item->getSpecialToDate()), 0, -3);
        $value = '';
        if ($from && $to) {
            $value .= $from . $sign . $offset . '/' . $to . $sign . $offset;
        }
        return $value;
    }
    /**
     * {min_price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return float the min price for bundle / configurable products
     */
    public function minPrice($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $price = $item->getMinPrice();
        if ($price <= 0) {
            return null;
        }
        return $this->applyTaxThenCurrency($model, $item->getTaxClassId(), $price, $options, $reference);
    }
    /**
     * {max_price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return float the max price for bundle / configurable products
     */
    public function maxPrice($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $price = $item->getMaxPrice();
        if ($price <= 0) {
            return null;
        }
        return $this->applyTaxThenCurrency($model, $item->getTaxClassId(), $price, $options, $reference);
    }
    /**
     * {special_price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return float the special of a product if it exists, the normal price else
     */
    public function specialPrice($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $price = null;
        if ($item->getSpecialFromDate() && !$item->getSpecialToDate()) {
            if ($item->getSpecialFromDate() <= $this->coreDate->date('Y-m-d H:i:s')) {
                if ($item->getTypeId() == 'bundle') {
                    if ($item->getPriceType()) {
                        $price = number_format($item->getPrice() * $item->getSpecialPrice() / 100, 2, ".", "");
                    } else {
                        $price = $item->getSpecialPrice();
                    }
                } else {
                    $price = $item->getSpecial_price();
                }
            }
        } elseif ($item->getSpecialFromDate() && $item->getSpecialToDate()) {
            if ($item->getSpecialFromDate() <= $this->coreDate->date('Y-m-d H:i:s') && $this->coreDate->date('Y-m-d H:i:s') < $item->getSpecialToDate()) {
                if ($item->getTypeId() == 'bundle') {
                    if ($item->getPriceType()) {
                        $price = number_format($item->getPrice() * $item->getSpecialPrice() / 100, 2, '.', '');
                    } else {
                        $price = $item->getSpecialPrice();
                    }
                } else {
                    $price = $item->getSpecial_price();
                }
            }
        } else {
            if ($item->getTypeId() == 'bundle') {
                if ($item->getPriceType()) {
                    $price = number_format($item->getPrice() * $item->getSpecialPrice() / 100, 2, '.', '');
                } else {
                    $price = $item->getSpecialPrice();
                }
            } else {
                $price = $item->getSpecial_price();
            }
        }
        if ($price > 0) {
            $value = $this->applyTaxThenCurrency($model, $item->getTaxClassId(), $price, $options, $reference);
        } else {
            $value = '';
        }
        return $value;
    }
    /**
     * {price_rules} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string|float price defined by catalog price rules if existing, special price else, normal price else
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function priceRules($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $timestamp = $this->localeDate->scopeDate($model->params['store_id']);
        $websiteId = $model->storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $rulePrice = $this->ruleFactory->create()->getRulePrice($timestamp, $websiteId, $customerGroupId, $item->getId());
        if ($rulePrice !== false) {
            $priceRules = sprintf('%.2f', round($rulePrice, 2));
        } else {
            $priceRules = '';
        }
        if ($priceRules != '') {
            $value = $this->applyTaxThenCurrency($model, $item->getTaxClassId(), $priceRules, $options, $reference);
        } else {
            $value = '';
        }
        return $value;
    }
    public function hasSalePrice($model, $options, $product, $reference)
    {
        return $this->salePrice($model, $options, $product, $reference) != '';
    }
    /**
     * {is_special_price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return int|string 0 if there is a special price, 0 else
     */
    public function hasSpecialPrice($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $true = !isset($options['yes']) ? 1 : $options['yes'];
        $false = !isset($options['no']) ? 0 : $options['no'];
        if ($item->getSpecialFromDate() && !$item->getSpecialToDate()) {
            if ($item->getSpecialFromDate() <= $this->coreDate->date('Y-m-d H:i:s')) {
                if ($item->getTypeID() == 'bundle') {
                    $value = ($item->getPriceType() || !$item->getPriceType() && $item->getSpecialPrice() < $item->getPrice()) && $item->getSpecialPrice() > 0 ? $true : $false;
                } else {
                    $value = $item->getSpecialPrice() && $item->getSpecialPrice() < $item->getPrice() ? $true : $false;
                }
            } else {
                if ($item->getTypeID() == 'bundle') {
                    $value = $false;
                } else {
                    $value = $false;
                }
            }
        } elseif ($item->getSpecialFromDate() && $item->getSpecialToDate()) {
            if ($item->getSpecialFromDate() <= $this->coreDate->date('Y-m-d H:i:s') && $this->coreDate->date('Y-m-d H:i:s') < $item->getSpecialToDate()) {
                if ($item->getTypeID() == 'bundle') {
                    $value = ($item->getPriceType() || !$item->getPriceType() && $item->getSpecialPrice() < $item->getPrice()) && $item->getSpecialPrice() > 0 ? $true : $false;
                } else {
                    $value = $item->getSpecialPrice() && $item->getSpecialPrice() < $item->getPrice() ? $true : $false;
                }
            } else {
                if ($item->getTypeID() == 'bundle') {
                    $value = $false;
                } else {
                    $value = $false;
                }
            }
        } else {
            if ($item->getTypeID() == 'bundle') {
                $value = ($item->getPriceType() || !$item->getPriceType() && $item->getSpecialPrice() < $item->getPrice()) && $item->getSpecialPrice() > 0 ? $true : $false;
            } else {
                $value = $item->getSpecialPrice() && $item->getSpecialPrice() < $item->getPrice() ? $true : $false;
            }
        }
        return $value;
    }
    /**
     * {price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return float the price of the product
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function salePrice($model, $options, $product, $reference)
    {
        $priceRules = $this->priceRules($model, $options, $product, $reference);
        $specialPrice = $this->specialPrice($model, $options, $product, $reference);
        if ($priceRules != '' && $specialPrice != '') {
            if ($priceRules < $specialPrice) {
                return $priceRules;
            } else {
                return $specialPrice;
            }
        } elseif ($priceRules != '') {
            return $priceRules;
        } elseif ($specialPrice != '') {
            return $specialPrice;
        } else {
            return '';
        }
    }
    /**
     * {normal_price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return float the normal price of the product
     */
    public function normalPrice($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $price = $item->getPrice();
        if ($price <= 0) {
            return null;
        }
        return $this->applyTaxThenCurrency($model, $item->getTaxClassId(), $price, $options, $reference);
    }
    /**
     * {final_price} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string formatted version of the final price
     */
    public function finalPrice($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $price = $item->getFinalPrice();
        if ($price <= 0) {
            return null;
        }
        return $this->applyTaxThenCurrency($model, $item->getTaxClassId(), $price, $options, $reference);
    }
    /**
     * Apply vat rate and currency to a price
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param int $taxClassId the tax class id
     * @param float $price original price
     * @param array $options attribute options
     * @param string $reference parent reference
     * @return float the final price
     */
    public function applyTaxThenCurrency($model, $taxClassId, $price, $options, $reference)
    {
        unset($reference);
        $vat = !isset($options['vat_rate']) ? false : $options['vat_rate'];
        $valueTax = $this->applyTax($model, $price, $model->priceIncludesTax, $taxClassId, $vat);
        $currency = !isset($options['currency']) ? $model->defaultCurrency : $options['currency'];
        $valueCur = $this->applyCurrencyRate($model, $valueTax, $currency);
        return number_format($valueCur, 2, '.', '');
    }
    /**
     * Apply a currency rate
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param float $price
     * @param string $currency
     * @return float
     */
    public function applyCurrencyRate($model, $price, $currency)
    {
        $currencies = $model->listOfCurrencies;
        if (isset($currencies[$currency])) {
            return $price * $currencies[$currency];
        } else {
            return $price;
        }
    }
    /**
     * Apply a vat tax rate
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param float $priceOrig the original price
     * @param bool $priceIncludeTax
     * @param int $taxClassId
     * @param bool|float|string $vat apply VAT ?
     * @return float
     */
    public function applyTax($model, $priceOrig, $priceIncludeTax, $taxClassId, $vat = false)
    {
        $rates = $model->taxRates;
        $price = number_format($priceOrig, 2, '.', '');
        if ($vat === false) {
            // $vat=false -> automatic
            // If multiple VAT, return the price without tax
            if (!$priceIncludeTax && isset($rates[$taxClassId])) {
                if (count($rates[$taxClassId]) > 1) {
                    return $price;
                } else {
                    // Unique tax > calculation of the price including tax
                    return $price * ($rates[$taxClassId][0]['rate'] / 100 + 1);
                }
            } else {
                return $price;
            }
        } elseif ($vat === "0") {
            // $vat=='0' -> exclude vat
            if ($priceIncludeTax && isset($rates[$taxClassId])) {
                // case 1: price including tax > extract it
                if (count($rates[$taxClassId]) > 1) {
                    // Multiple VAT > return price without tax
                    return $price;
                } else {
                    // Unique tax > remove it from the price
                    return 100 * $price / (100 + $rates[$taxClassId][0]['rate']);
                }
            } else {
                // case 2: price without tax
                return $price;
            }
        } else {
            // $vat==true -> force the tax
            if (is_numeric($vat)) {
                // $vat is_numeric
                if ($taxClassId != 0) {
                    // if tax calculation forces on taxed product
                    return $price * ($vat / 100 + 1);
                } elseif ($taxClassId == 0) {
                    // if tax calculation forced on product without tax
                    return $price;
                }
            } else {
                // $vat is_string
                $vat = explode('/', $vat);
                $rateToApply = 0;
                $rateToRemove = false;
                if (substr($vat[0], 0, 1) == "-") {
                    $vat[0] = substr($vat[0], 1);
                    $rateToRemove = true;
                }
                if (isset($rates[$taxClassId])) {
                    foreach ($rates[$taxClassId] as $rate) {
                        if ($rate['country'] == $vat[0]) {
                            if (!isset($vat[1]) || $rate['code'] == $vat[1]) {
                                $rateToApply = $rate['rate'];
                                break;
                            }
                        }
                    }
                    if (!$rateToRemove) {
                        return $price * ($rateToApply / 100 + 1);
                    } else {
                        return 100 * $price / (100 + $rateToApply);
                    }
                } else {
                    return $price;
                }
            }
        }
    }
}
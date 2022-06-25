<?php

namespace Magecomp\Extrafee\Plugin\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Meetanshi\ShippingRates\Model\MethodFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\ProductFactory;
use Meetanshi\ShippingRates\Helper\Data;
use Magecomp\Extrafee\Helper\Data as ExtraFeeHelper;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollection;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Checkout\Model\Session;
use Magento\Framework\Webapi\Rest\Request;

class ModifiedRate extends \Meetanshi\ShippingRates\Model\Rate
{
    const MAX_LENGTH = 50000;
    const COLUMN_TOTAL = 19;
    const BATCH_SIZE = 50000;
    const COLUMN_COUNTRY = 0;
    const COLUMN_STATE = 1;
    const COLUMN_ZIP_FROM = 3;
    const COLUMN_ZIP_TO = 4;
    const COLUMN_PRICE_TO = 6;
    const COLUMN_WEIGHT_TO = 8;
    const COLUMN_QTY_TO = 10;
    const COLUMN_NUM_ZIP_FROM = 17;
    const COLUMN_NUM_ZIP_TO = 18;
    const COLUMN_SHIPPING_TYPE = 11;

    protected $methodFactory;
    protected $scopeConfig;
    protected $productFactory;
    protected $helper;
    protected $regionCollection;
    protected $countryCollection;
    protected $localeLists;
    protected $qtyRates=[];
    protected $_methodId;
    protected $checkoutSession;
    protected $request;
    protected $extraFeeHelper;

    public function __construct(Context $context, Registry $registry, MethodFactory $methodFactory, ScopeConfigInterface $scopeConfig, ProductFactory $productFactory, Data $helper, RegionCollection $regionCollection, CountryCollection $countryCollection, ListsInterface $localeLists, Session $checkoutSession, Request $request, ExtraFeeHelper $extraFeeHelper)
    {
        $this->scopeConfig = $scopeConfig;
        $this->methodFactory = $methodFactory;
        $this->productFactory = $productFactory;
        $this->countryCollection = $countryCollection;
        $this->regionCollection = $regionCollection;
        $this->helper = $helper;
        $this->localeLists = $localeLists;
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
        $this->extraFeeHelper = $extraFeeHelper;

        parent::__construct($context, $registry,$methodFactory,$scopeConfig,$productFactory,$helper,$regionCollection,$countryCollection,$localeLists);
    }

    public function calculate($request, $collection)
    {
        if (!$request->getAllItems()) {
            return [];
        }

        if ($collection->getSize() == 0) {
            return [];
        }

        $methodIds = [];
        foreach ($collection as $method) {
            $methodIds[] = $method->getMethodId();
        }

        $isFreePromo = $this->helper->allowPromo();
        $ignoreVirtual = $this->helper->ignoreVirtual();

        $items = $request->getAllItems();
        $shippingTypes = [];
        $shippingTypes[] = 0;
        foreach ($items as $item)
        {
            $product = $this->productFactory->create()->load($item->getProduct()->getEntityId());
            if ($product->getShippingType()) {
                $shippingTypes[] = $product->getShippingType();
            } else {
                $shippingTypes[] = 0;
            }
        }

        $shippingTypes = array_unique($shippingTypes);
        $shippingCosts = [];

        $shippingRates = $this->getResourceCollection();
        $shippingRates->addMethodFilters($methodIds);
        $ratesTypes = [];

        foreach ($shippingRates as $rate) {
            $ratesTypes[$rate->getMethodId()][]= $rate->getShippingType();
        }

        $intersectTypes = [];
        $adultPriceRates =  $this->extraFeeHelper->getShippingRatesAdultPrice();
        $additional =  $this->extraFeeHelper->getAdditionalFee();
        foreach ($ratesTypes as $key => $value)
        {
            $intersectTypes[$key] = array_intersect($shippingTypes, $value);
            arsort($intersectTypes[$key]);
            $methodIds = [$key];
            $this->_methodId = $key;
            $shippingTotals =  $this->calculateTotals($request, $ignoreVirtual, $isFreePromo, '0');

            foreach ($intersectTypes[$key] as $shippingType)
            {
                $totals = $this->calculateTotals($request, $ignoreVirtual, $isFreePromo, $shippingType);
                $totalQtys = $totals['qty'];
                if ($shippingTotals['qty'] > 0 && (!$this->helper->dontSplit() || $shippingTotals['qty'] == $totals['qty'])) {
                    if ($shippingType == 0) {
                        $totals = $shippingTotals;
                    }

                    $shippingTotals['not_free_price'] -= $totals['not_free_price'];
                    $shippingTotals['not_free_weight'] -= $totals['not_free_weight'];
                    $shippingTotals['not_free_qty'] -= $totals['not_free_qty'];
                    $shippingTotals['qty'] -= $totals['qty'];

                    $shippingRates = $this->getResourceCollection();
                    $shippingRates->addAddressFilters($request);
                    //$shippingRates->addTotalsFilters($totals, $shippingType);
                    $shippingRates->addMethodFilters($methodIds);

                    foreach ($this->calculateCosts($shippingRates, $totals, $request, $shippingType) as $key => $cost)
                    {
                        $method = $this->methodFactory->create()->load($key);
                        if (empty($shippingCosts[$key])) {
                            $shippingCosts[$key]['cost'] = $cost['cost'];
                            $shippingCosts[$key]['time'] = $cost['time'];
                        } else {
                            switch ($method->getSelectRate()) {
                                case '1':
                                    if ($shippingCosts[$key]['cost'] < $cost['cost']) {
                                        $shippingCosts[$key]['cost'] = $cost['cost'];
                                        $shippingCosts[$key]['time'] = $cost['time'];
                                    }
                                    break;
                                case '2':
                                    if ($shippingCosts[$key]['cost'] > $cost['cost']) {
                                        $shippingCosts[$key]['cost'] = $cost['cost'];
                                        $shippingCosts[$key]['time'] = $cost['time'];
                                    }
                                    break;
                                default:
                                    $shippingCosts[$key]['cost'] += $cost['cost'];
                                    $shippingCosts[$key]['time'] = $cost['time'];
                            }
                        }
                    }
                }
            }
            try{
                $qtyRates = $this->qtyRates[$this->_methodId];
                ksort($qtyRates);
                $last = end($qtyRates);
                $lastQty = $last['qty_to'];

                $rest = intval($totalQtys%$lastQty);
                $_rest = intval($totalQtys/$lastQty);
                $cost=0;

                if(isset($qtyRates[$totalQtys])){
                    $shippingCosts[$this->_methodId]['cost']=$qtyRates[$totalQtys]['cost_base'];
                }elseif($_rest){
                    if(isset($qtyRates[$lastQty])){
                        $cost += $_rest*$qtyRates[$lastQty]['cost_base'];
                    }
                    if(isset($qtyRates[$rest])){
                        $cost += $qtyRates[$rest]['cost_base'];
                    }
                    if(isset($shippingCosts[$this->_methodId]) && $cost){
                        $shippingCosts[$this->_methodId]['cost']=$cost;
                    }
                }
                $addType = $this->getAddressType();
                if(isset($adultPriceRates[$this->_methodId])){
                    if(isset($adultPriceRates[$this->_methodId][$addType])){
                        $shippingCosts[$this->_methodId]['cost'] = $shippingCosts[$this->_methodId]['cost']+$adultPriceRates[$this->_methodId][$addType]['rate']+$adultPriceRates[$this->_methodId][$addType]['adultfee'];
                    }
                }
                if(isset($additional[$this->_methodId])){
                    $shippingCosts[$this->_methodId]['cost'] = $shippingCosts[$this->_methodId]['cost']+$additional[$this->_methodId];
                }
            }catch(\Exception $e){}
        }
        
        return $shippingCosts;
    }

    protected function calculateTotals($request, $ignoreVirtual, $isFreePromo, $shippingType)
    {
        $totals = $this->initTotals();

        foreach ($request->getAllItems() as $item) {
            $product = $this->productFactory->create()->load($item->getProduct()->getEntityId());
            if (($product->getShippingType() != $shippingType) && ($shippingType != 0)) {
                continue;
            }

            if ($item->getParentItemId()) {
                continue;
            }

            if ($ignoreVirtual && $item->getProduct()->isVirtual()) {
                continue;
            }

            if ($item->getHasChildren()) {
                $qty = 0;
                $notFreeQty = 0;
                $price = 0;
                $weight = 0;
                $itemQty = 0;

                foreach ($item->getChildren() as $child) {
                    $itemQty = $child->getQty() * $item->getQty();
                    $qty += $itemQty;
                    $notFreeQty += ($itemQty - $this->getFreeQty($child, $isFreePromo));
                    $price += $child->getPrice() * $itemQty;
                    $weight += $child->getWeight() * $itemQty;
                    $totals['tax_amount'] += $child->getBaseTaxAmount() + $child->getBaseHiddenTaxAmount();
                    $totals['discount_amount'] += $child->getBaseDiscountAmount();
                }

                if ($item->getProductType() == 'bundle') {
                    $qty = $item->getQty();

                    if ($item->getProduct()->getWeightType() == 1) {
                        $weight = $item->getWeight();
                    }

                    if ($item->getProduct()->getPriceType() == 1) {
                        $price = $item->getPrice();
                    }

                    if ($item->getProduct()->getSkuType() == 1) {
                        $totals['tax_amount'] += $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount();
                        $totals['discount_amount'] += $item->getBaseDiscountAmount();
                    }

                    $notFreeQty = ($qty - $this->getFreeQty($item, $isFreePromo));
                    $totals['qty'] += $qty;
                    $totals['not_free_qty'] += $notFreeQty;
                    $totals['not_free_price'] += $price;
                    $totals['not_free_weight'] += $weight;
                } elseif ($item->getProductType() == 'configurable') {
                    $qty = $item->getQty();
                    $price = $item->getPrice();
                    $weight = $item->getWeight();
                    $notFreeQty = ($qty - $this->getFreeQty($item, $isFreePromo));
                    $totals['qty'] += $qty;
                    $totals['not_free_qty'] += $notFreeQty;
                    $totals['not_free_price'] += $price * $notFreeQty;
                    $totals['not_free_weight'] += $weight * $notFreeQty;
                    $totals['tax_amount'] += $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount();
                    $totals['discount_amount'] += $item->getBaseDiscountAmount();
                } else {
                    $qty = $item->getQty();
                    $price = $item->getPrice();
                    $weight = $item->getWeight();
                    $notFreeQty = ($qty - $this->getFreeQty($item, $isFreePromo));
                    $totals['qty'] += $qty;
                    $totals['not_free_qty'] += $notFreeQty;
                    $totals['not_free_price'] += $price * $notFreeQty;
                    $totals['not_free_weight'] += $weight * $notFreeQty;
                }
            } else {
                $qty = $item->getQty();
                $notFreeQty = ($qty - $this->getFreeQty($item, $isFreePromo));
                $totals['not_free_price'] += $item->getBasePrice() * $notFreeQty;
                $totals['not_free_weight'] += $item->getWeight() * $notFreeQty;
                $totals['qty'] += $qty;
                $totals['not_free_qty'] += $notFreeQty;
                $totals['tax_amount'] += $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount();
                $totals['discount_amount'] += $item->getBaseDiscountAmount();
            }
        }

        if ($totals['qty'] != $totals['not_free_qty']) {
            $request->setFreeShipping(false);
        }

        $afterDiscount = $this->helper->getAfterDiscount();
        $includingTax = $this->helper->getIncludingTax();

        if ($afterDiscount) {
            $totals['not_free_price'] -= $totals['discount_amount'];
        }

        if ($includingTax) {
            $totals['not_free_price'] += $totals['tax_amount'];
        }

        if ($totals['not_free_price'] < 0) {
            $totals['not_free_price'] = 0;
        }

        if ($request->getFreeShipping() && $isFreePromo) {
            $totals['not_free_price'] = $totals['not_free_weight'] = $totals['not_free_qty'] = 0;
        }

        return $totals;
    }

    protected function calculateCosts($rates, $totals, $request, $shippingType)
    {
        $shippingParams = ['country', 'state', 'city'];
        $rangeParams = ['price', 'qty', 'weight'];

        $minCounts = [];
        $results = [];
        
        foreach ($rates as $rate) {
            $rate = $rate->getData();
            $this->qtyRates[$this->_methodId][$rate['qty_to']] = $rate;
            $valuesCount = 0;

            if (empty($rate['shipping_type'])) {
                $valuesCount++;
            }

            foreach ($shippingParams as $param) {
                if (empty($rate[$param])) {
                    $valuesCount++;
                }
            }

            foreach ($rangeParams as $param) {
                if ((ceil($rate[$param . '_from']) == 0) && (ceil($rate[$param . '_to']) == 999999)) {
                    $valuesCount++;
                }
            }

            if (empty($rate['zip_from']) && empty($rate['zip_to'])) {
                $valuesCount++;
            }

            if (!$totals['not_free_price'] && !$totals['not_free_qty'] && !$totals['not_free_weight']) {
                $cost = 0;
            } else {
                $cost = $rate['cost_base'] + $totals['not_free_price'] * $rate['cost_percent'] / 100 + $totals['not_free_qty'] * $rate['cost_product'] + $totals['not_free_weight'] * $rate['cost_weight'];
            }

            $id = $rate['method_id'];
            if ((empty($minCounts[$id]) && empty($results[$id])) || ($minCounts[$id] > $valuesCount) || (($minCounts[$id] == $valuesCount) && ($cost > $results[$id]))) {
                $qtyRates[]=$rate;
                $minCounts[$id] = $valuesCount;
                $results[$id]['cost'] = $cost;
                $results[$id]['time'] = $rate['time_delivery'];
            }
        }

        return $results;
    }

    public function getAddressType(){
        $post = $this->request->getBodyParams();
        $type='';
        $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();
        if($shippingAddress->getType()){
            $type=$shippingAddress->getType();
        }
        if(isset($post['address'])){
            if(isset($post['address']['custom_attributes'])){
                foreach($post['address']['custom_attributes'] as $att){
                    if($att['attribute_code']==\Magecomp\Extrafee\Model\Source\AddressType::ADDRESS_TYPE_CODE){
                        $type=$att['value'];
                    }
                }
            }
        }
        if(isset($post['shippingAddress'])){
            if(isset($post['shippingAddress']['customAttributes'])){
                $customAttr = $post['shippingAddress']['customAttributes'];
                foreach($customAttr as $att){
                    if($att['attribute_code']==\Magecomp\Extrafee\Model\Source\AddressType::ADDRESS_TYPE_CODE){
                        $type=$att['value'];
                    }
                }
            }
        }
        return $type;
    }
}

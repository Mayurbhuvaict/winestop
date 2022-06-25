<?php

namespace Meetanshi\ShippingRates\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Meetanshi\ShippingRates\Model\MethodFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\ProductFactory;
use Meetanshi\ShippingRates\Helper\Data;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollection;
use Magento\Framework\Locale\ListsInterface;
use Magento\Framework\Model\AbstractModel;

class Rate extends AbstractModel
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

    public function __construct(Context $context, Registry $registry, MethodFactory $methodFactory, ScopeConfigInterface $scopeConfig, ProductFactory $productFactory, Data $helper, RegionCollection $regionCollection, CountryCollection $countryCollection, ListsInterface $localeLists)
    {
        $this->scopeConfig = $scopeConfig;
        $this->methodFactory = $methodFactory;
        $this->productFactory = $productFactory;
        $this->countryCollection = $countryCollection;
        $this->regionCollection = $regionCollection;
        $this->helper = $helper;
        $this->localeLists = $localeLists;

        parent::__construct($context, $registry);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Meetanshi\ShippingRates\Model\ResourceModel\Rate');
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

        foreach ($ratesTypes as $key => $value)
        {
            $intersectTypes[$key] = array_intersect($shippingTypes, $value);
            arsort($intersectTypes[$key]);
            $methodIds = [$key];
            $shippingTotals =  $this->calculateTotals($request, $ignoreVirtual, $isFreePromo, '0');

            foreach ($intersectTypes[$key] as $shippingType)
            {
                $totals = $this->calculateTotals($request, $ignoreVirtual, $isFreePromo, $shippingType);
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
                    $shippingRates->addTotalsFilters($totals, $shippingType);
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

    public function initTotals()
    {
        $totals = [
            'not_free_price' => 0,
            'not_free_weight' => 0,
            'qty' => 0,
            'not_free_qty' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
        ];
        return $totals;
    }

    public function getFreeQty($item, $isFreePromo)
    {
        $freeQty = 0;
        if ($item->getFreeShipping() && $isFreePromo) {
            $freeQty = ((is_numeric($item->getFreeShipping())) && ($item->getFreeShipping() <= $item->getQty())) ? $item->getFreeShipping() : $item->getQty();
        }

        return $freeQty;
    }

    protected function calculateCosts($rates, $totals, $request, $shippingType)
    {
        $shippingParams = ['country', 'state', 'city'];
        $rangeParams = ['price', 'qty', 'weight'];

        $minCounts = [];
        $results = [];
        foreach ($rates as $rate) {
            $rate = $rate->getData();
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
                $minCounts[$id] = $valuesCount;
                $results[$id]['cost'] = $cost;
                $results[$id]['time'] = $rate['time_delivery'];
            }
        }
        return $results;
    }

    public function import($methodId, $fileName)
    {
        $err = [];

        $fp = fopen($fileName, 'r');
        if (!$fp) {
            $err[] = __('Can not open file %s .', $fileName);
            return $err;
        }
        $methodId = (int)$methodId;
        if (!$methodId) {
            $err[] = __('Specify a valid method ID.');
            return $err;
        }

        $countryIsoCodes = $this->getCountries();
        $stateCodes = $this->getStates();
        $countryNames = $this->getCountriesName();
        $statesName = $this->getStatesName();
        $shippingLabels = $this->helper->getShippingType();

        $data = [];
        $dataIndex = 0;

        $currLineNum = 0;
        while (($line = fgetcsv($fp, self::MAX_LENGTH, ',', '"')) !== false)
        {
            $currLineNum++;

            if ($currLineNum == 1) {
                continue;
            }

            if ((count($line)+2) != self::COLUMN_TOTAL) {
                $err[] = 'Line #' . $currLineNum . ': warning, expected number of columns is ' . self::COLUMN_TOTAL;
                if (count($line) > self::COLUMN_TOTAL) {
                    for ($i = 0; $i < count($line) - self::COLUMN_TOTAL; $i++) {
                        unset($line[self::COLUMN_TOTAL + $i]);
                    }
                }

                if (count($line) < self::COLUMN_TOTAL) {
                    for ($i = 0; $i < self::COLUMN_TOTAL - count($line); $i++) {
                        $line[count($line) + $i] = 0;
                    }
                }
            }

            $zipFrom = $this->helper->getNumericZip($line[self::COLUMN_ZIP_FROM]);
            $zipTo = $this->helper->getNumericZip($line[self::COLUMN_ZIP_TO]);
            $line[self::COLUMN_NUM_ZIP_FROM] = $zipFrom['district'];
            $line[self::COLUMN_NUM_ZIP_TO] = $zipTo['district'];

            for ($i = 0; $i < self::COLUMN_TOTAL-2; $i++) {
                $line[$i] = str_replace(["\r", "\n", "\t", "\\", '"', "'", "*"], '', $line[$i]);
            }

            $countries = [''];
            if ($line[self::COLUMN_COUNTRY]) {
                $countries = explode(',', $line[self::COLUMN_COUNTRY]);
            } else {
                $line[self::COLUMN_COUNTRY] = '0';
            }

            $states = [''];
            if ($line[self::COLUMN_STATE]) {
                $states = explode(',', $line[self::COLUMN_STATE]);
            }

            $types = [''];
            if ($line[self::COLUMN_SHIPPING_TYPE]) {
                $types = explode(',', $line[self::COLUMN_SHIPPING_TYPE]);
            }

            $zips = [''];
            if ($line[self::COLUMN_ZIP_FROM]) {
                $zips = explode(',', $line[self::COLUMN_ZIP_FROM]);
            }

            if (!$line[self::COLUMN_PRICE_TO]) {
                $line[self::COLUMN_PRICE_TO] = 99999999;
            }
            if (!$line[self::COLUMN_WEIGHT_TO]) {
                $line[self::COLUMN_WEIGHT_TO] = 99999999;
            }
            if (!$line[self::COLUMN_QTY_TO]) {
                $line[self::COLUMN_QTY_TO] = 99999999;
            }

            foreach ($types as $type) {
                if ($type == 'All') {
                    $type = 0;
                }
                if ($type && empty($shippingLabels[$type])) {
                    if (in_array($type, $shippingLabels)) {
                        $shippingLabels[$type] = array_search($type, $shippingLabels);
                    } else {
                        $err[] = 'Line #' . $currLineNum . ': invalid type code ' . $type;
                        continue;
                    }
                }
                $line[self::COLUMN_SHIPPING_TYPE] = $type ? $shippingLabels[$type] : '';
            }

            foreach ($countries as $country) {
                if ($country == 'All') {
                    $country = 0;
                }

                if ($country && empty($countryIsoCodes[$country])) {
                    if (in_array($country, $countryNames)) {
                        $countryIsoCodes[$country] = array_search($country, $countryNames);
                    } else {
                        $err[] = 'Line #' . $currLineNum . ': invalid country code ' . $country;
                        continue;
                    }
                }
                $line[self::COLUMN_COUNTRY] = $country ? $countryIsoCodes[$country] : '0';


                foreach ($states as $state) {
                    if ($state == 'All') {
                        $state = '';
                    }

                    if ($state && empty($stateCodes[$state][$country])) {
                        if (in_array($state, $statesName)) {
                            $stateCodes[$state][$country] = array_search($state, $statesName);
                        } else {
                            $err[] = 'Line #' . $currLineNum . ': invalid state code ' . $state;
                            continue;
                        }
                    }
                    $line[self::COLUMN_STATE] = $state ? $stateCodes[$state][$country] : '';

                    foreach ($zips as $zip) {
                        $line[self::COLUMN_ZIP_FROM] = $zip;
                        $data[$dataIndex] = $line;
                        $dataIndex++;

                        if ($dataIndex > self::BATCH_SIZE) {
                            $errText = $this->getResource()->bulkInsert($methodId, $data);
                            if ($errText) {
                                $err[] = 'Line #' . $currLineNum . ': duplicated conditions before this line have been skipped';
                            }
                            $data = [];
                            $dataIndex = 0;
                        }
                    }
                }
            }
        }
        fclose($fp);

        if ($dataIndex) {
            $errText = $this->getResource()->bulkInsert($methodId, $data);

            if ($errText) {
                $err[] = 'Line #' . $currLineNum . ': duplicated conditions before this line have been skipped';
            }
        }
        return $err;
    }

    public function getCountries()
    {
        $hash = [];

        $collection = $this->countryCollection->create();
        foreach ($collection as $item) {
            $hash[$item['iso3_code']] = $item['country_id'];
            $hash[$item['iso2_code']] = $item['country_id'];
        }
        return $hash;
    }

    public function getStates()
    {
        $hash = [];
        $collection = $this->regionCollection->create();
        foreach ($collection as $state) {
            $hash[$state['code']][$state['country_id']] = $state['region_id'];
        }
        return $hash;
    }

    public function getCountriesName()
    {
        $hash = [];
        $collection = $this->countryCollection->create();
        foreach ($collection as $item) {
            $country_name = $this->localeLists->getCountryTranslation($item['iso2_code']);
            $hash[$item['country_id']] = $country_name;
        }
        return $hash;
    }

    public function getStatesName()
    {
        $hash = [];
        $collection = $this->regionCollection->create();
        $countryHash = $this->getCountriesName();
        foreach ($collection as $state) {
            $string = $countryHash[$state['country_id']] . '/' . $state['default_name'];
            $hash[$state['region_id']] = $string;
        }
        return $hash;
    }

    public function deleteBy($methodId)
    {
        return $this->getResource()->deleteBy($methodId);
    }
}

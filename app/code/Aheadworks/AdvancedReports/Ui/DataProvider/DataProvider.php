<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */









































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider;

use Aheadworks\AdvancedReports\Model\Filter\FilterPool;
use Aheadworks\AdvancedReports\Ui\ScopeCurrency;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Api\Search\ReportingInterface;
use Aheadworks\AdvancedReports\Model\Url\Base64Coder;

/**
 * Class DataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var ScopeCurrency
     */
    private $scopeCurrency;

    /**
     * @var SearchResultInterface
     */
    private $compareSearchResultCached;

    /**
     * @var SearchResultInterface
     */
    private $searchResultCached;

    /**
     * @var array
     */
    private $exportParams = [];

    /**
     * @var array
     */
    private $allowedRequestParams = [];

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param FormatInterface $localeFormat
     * @param ScopeCurrency $scopeCurrency
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        FormatInterface $localeFormat,
        ScopeCurrency $scopeCurrency,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->localeFormat = $localeFormat;
        $this->scopeCurrency = $scopeCurrency;
    }

    /**
     * Retrieve allowed params from request
     *
     * @return array
     */
    public function getAllowedRequestParams()
    {
        return $this->allowedRequestParams;
    }

    /**
     * Retrieve provider data
     *
     * @return array
     */
    public function getProviderData()
    {
        return $this->data;
    }

    /**
     * Retrieve cached Search Result
     *
     * @return SearchResultInterface
     */
    public function getSearchResultCached()
    {
        return $this->searchResultCached;
    }

    /**
     * Retrieve cached compare Search Result
     *
     * @return SearchResultInterface
     */
    public function getCompareSearchResultCached()
    {
        return $this->compareSearchResultCached;
    }

    /**
     * Retrieve default filter pool
     *
     * @return FilterPool
     */
    public function getDefaultFilterPool()
    {
        return $this->reporting->getDefaultFilterPool();
    }

    /**
     * Check if enabled compare to
     *
     * @return bool
     */
    public function isEnabledCompareTo()
    {
        $periodFilter = $this->getDefaultFilterPool()->getFilter('period');

        return $this->isAvailableCompareTo() ? $periodFilter->isCompareEnabled() : false;
    }

    /**
     * Check if compare to available
     *
     * @return bool
     */
    public function isAvailableCompareTo()
    {
        $config = $this->getConfigData();

        return isset($config['compareToAvailable']) && !$config['compareToAvailable'] ? false : true;
    }

    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        $storeFilter = $this->getDefaultFilterPool()->getFilter('store');

        return $this->scopeCurrency->getCurrencyCode($storeFilter->getStoreIds());
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            $addToFilter = true;
            $addToGridRowUrl = false;
            $decode = false;
            if (is_array($paramValue)) {
                $addToFilter = isset($paramValue['addToFilter']) ? $paramValue['addToFilter'] : $addToFilter;
                $decode = isset($paramValue['decode']) ? $paramValue['decode'] : $decode;
                $addToGridRowUrl = isset($paramValue['useParamInGridRowUrl'])
                    ? $paramValue['useParamInGridRowUrl']
                    : $addToGridRowUrl;
                $paramValue = $paramValue['value'];
            }
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }
            if ($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );
                if ($addToGridRowUrl) {
                    $this->allowedRequestParams[$paramName] = $paramValue;
                }
                if ($addToFilter) {
                    $this->exportParams[$paramName] = $paramValue;
                    // For product variant performance report
                    if ($paramName == 'product_id') {
                        $isProductConversionVariant = isset($this->data['config']['report_id']) ?
                            $this->data['config']['report_id'] == 'productconversion_variant' :
                            false;
                        if (!$isProductConversionVariant) {
                            $parentId = $this->request->getParam('parent_id');
                            $paramValue = ['product_id' => $paramValue, 'parent_id' => $parentId];
                        } else {
                            $paramValue = ['product_id' => $paramValue];
                        }
                    }
                    if ($decode) {
                        $paramValue = Base64Coder::decode($paramValue);
                    }
                    $this->addFilter(
                        $this->filterBuilder
                            ->setField($paramName)
                            ->setValue($paramValue)
                            ->setConditionType('eq')
                            ->create()
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $this->cachedCompareSearchResult($searchResult);

        $arrItems = parent::searchResultToOutput($searchResult);
        $this->cachedSearchResult($searchResult);
        $arrItems['totals'][] = $searchResult->getTotals();
        $arrItems['priceFormat'] = $this->localeFormat->getPriceFormat(null, $this->getCurrencyCode());
        $arrItems['basePriceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $this->scopeCurrency->getCurrencyCode(null)
        );
        $arrItems['exportParams'] = $this->exportParams;
        $this->attachFilterData($arrItems);

        if ($arrItems['compareEnabled']) {
            $this->attachCompareData($arrItems);
        }

        return $arrItems;
    }

    /**
     * Cached compare search result
     *
     * @param SearchResultInterface $searchResult
     * @return $this
     */
    private function cachedCompareSearchResult($searchResult)
    {
        $this->compareSearchResultCached = clone $searchResult;

        return $this;
    }

    /**
     * Cached search result
     *
     * @param SearchResultInterface $searchResult
     * @return $this
     */
    private function cachedSearchResult($searchResult)
    {
        $this->searchResultCached = $searchResult;

        return $this;
    }

    /**
     * Attach filters data
     *
     * @param array $arrItems
     * @return $this
     */
    private function attachFilterData(&$arrItems)
    {
        $groupByFilter = $this->getDefaultFilterPool()->getFilter('group_by');
        $customerGroupFilter = $this->getDefaultFilterPool()->getFilter('customer_group');
        $periodFilter = $this->getDefaultFilterPool()->getFilter('period');

        $arrItems['groupByFilter'] = $groupByFilter->getValue();
        $arrItems['customerGroupFilter'] = $customerGroupFilter->getValue();
        $arrItems['periodFromFilter'] = $periodFilter->getPeriodFrom();
        $arrItems['periodToFilter'] = $periodFilter->getPeriodTo();
        $arrItems['comparePeriodFromFilter'] = $periodFilter->getCompareFrom();
        $arrItems['comparePeriodToFilter'] = $periodFilter->getCompareTo();
        $arrItems['compareEnabled'] = $this->isEnabledCompareTo();

        return $this;
    }

    /**
     * Attach compare data
     *
     * @param array $arrItems
     * @return $this
     */
    private function attachCompareData(&$arrItems)
    {
        $compareSearchResult = $this->getCompareSearchResultCached();
        $compareSearchResult->enableCompareMode()->getData();

        /*
        $arrItems['compare_items'] = [];
        foreach ($compareSearchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $arrItems['compare_items'][] = $itemData;
        }*/
        $arrItems['compare_totals'][] = $compareSearchResult->getTotals();

        return $this;
    }
}

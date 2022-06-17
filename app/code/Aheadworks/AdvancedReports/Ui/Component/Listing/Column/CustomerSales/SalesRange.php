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





































































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\CustomerSales;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;

/**
 * Class SalesRange
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\CustomerSales
 */
class SalesRange extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlModel $urlModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlModel $urlModel,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlModel = $urlModel;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $excludeRefunded = $this->getExcludeRefunded();
        $format = $dataSource['data']['priceFormat'];

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $rangeFrom = number_format(
                    $item['range_from'],
                    $format['precision'],
                    $format['decimalSymbol'],
                    $format['groupSymbol']
                );
                $rangeTo = number_format(
                    $item['range_to'],
                    $format['precision'],
                    $format['decimalSymbol'],
                    $format['groupSymbol']
                );
                $fromPattern = str_replace('%s', '%1', $format['pattern']);
                $toPattern = str_replace('%s', '%2', $format['pattern']);
                if ($item['range_to']) {
                    $item['row_label'] = __($fromPattern . ' - ' . $toPattern, [$rangeFrom, $rangeTo]);
                } else {
                    $item['row_label'] = __($fromPattern . '+', [$rangeFrom]);
                }

                $params = [
                    'range_from' => $item['range_from'],
                    'range_to' => $item['range_to'],
                    'range_title' => base64_encode($item['row_label'])
                ];
                if ($excludeRefunded) {
                    $params['exclude_refunded'] = $excludeRefunded;
                }
                $item['row_url'] = $this->urlModel->getUrl(
                    'customersales',
                    'customersales_customers',
                    $dataSource['data']['periodFromFilter'],
                    $dataSource['data']['periodToFilter'],
                    $params
                );
            }

            foreach ($dataSource['data']['totals'] as &$total) {
                $rangeFrom = number_format(
                    0,
                    $format['precision'],
                    $format['decimalSymbol'],
                    $format['groupSymbol']
                );
                $total['row_label'] = __('All Sales (%1)', __($fromPattern . '+', [$rangeFrom]));
                $params = [
                    'range_from' => 0,
                    'range_title' => base64_encode($total['row_label'])
                ];
                if ($excludeRefunded) {
                    $params['exclude_refunded'] = $excludeRefunded;
                }
                $total['row_url'] = $this->urlModel->getUrl(
                    'customersales',
                    'customersales_customers',
                    $dataSource['data']['periodFromFilter'],
                    $dataSource['data']['periodToFilter'],
                    $params
                );
            }
        }
        return $dataSource;
    }

    /**
     * Get exclude refunded
     *
     * @return bool
     */
    private function getExcludeRefunded()
    {
        $reportSettingsFilter = $this->context->getDataProvider()->getDefaultFilterPool()->getFilter('report_settings');
        $includeRefunded = $reportSettingsFilter->getReportSettingParam('include_refunded_items') == 1;

        return !$includeRefunded;
    }
}

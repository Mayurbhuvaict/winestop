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



























































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Period as PeriodModel;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column
 */
class Period extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var PeriodModel
     */
    private $periodModel;

    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PeriodModel $periodModel
     * @param UrlModel $urlModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PeriodModel $periodModel,
        UrlModel $urlModel,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->periodModel = $periodModel;
        $this->urlModel = $urlModel;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $this->prepareItemRows($dataSource)->prepareChartRows($dataSource);

        return $dataSource;
    }

    /**
     * Prepare items
     *
     * @param array $dataSource
     * @return $this
     */
    private function prepareItemRows(&$dataSource)
    {
        $visible = null !== $this->getData('config/visible') ? $this->getData('config/visible') : true;
        if (!isset($dataSource['data']['items']) || !$visible) {
            return $this;
        }

        $isUrl = $this->getData('config/isUrl') ?: false;
        $groupBy = $dataSource['data']['groupByFilter'];
        foreach ($dataSource['data']['items'] as &$item) {
            $comparePeriodDates = [];
            $periodDates = $this->getPeriodDates($dataSource, $item, false);
            $periodLabel = $this->getPeriodLabel($periodDates, $groupBy);
            $item['row_label'] = $periodLabel;

            if ($dataSource['data']['compareEnabled']) {
                $comparePeriodDates = $this->getPeriodDates($dataSource, $item, true);
                $comparePeriodLabel = '';
                if (!empty($comparePeriodDates)) {
                    $comparePeriodLabel = $this->getPeriodLabel($comparePeriodDates, $groupBy);
                } else {
                    $item['display_compare'] = false;
                }
                $item['c_' . $this->getName()] = $comparePeriodLabel;
            }

            if ($isUrl) {
                $item['row_url'] = $this->getPeriodUrl($periodDates, $comparePeriodDates, $groupBy);
            }
        }

        return $this;
    }

    /**
     * Prepare chart rows
     *
     * @param array $dataSource
     * @return $this
     */
    private function prepareChartRows(&$dataSource)
    {
        if (!isset($dataSource['data']['chart']['rows'])) {
            return $this;
        }

        $groupBy = $dataSource['data']['groupByFilter'];
        foreach ($dataSource['data']['chart']['rows'] as &$item) {
            $periodDates = $this->getPeriodDates($dataSource, $item, false);
            $item[$this->getName()] = $this->getPeriodLabel($periodDates, $groupBy);

            if ($dataSource['data']['compareEnabled']) {
                $periodDates = $this->getPeriodDates($dataSource, $item, true);
                $periodLabel = '';
                if (!empty($periodDates)) {
                    $periodLabel = $this->getPeriodLabel($periodDates, $groupBy);
                }
                $item['c_' . $this->getName()] = $periodLabel;
            }
        }

        return $this;
    }

    /**
     * Retrieve period
     *
     * @param array $dataSource
     * @param array $item
     * @param bool $isCompare
     * @return array
     */
    private function getPeriodDates($dataSource, $item, $isCompare)
    {
        $periodFilterPrefix = $isCompare ? 'compareP' : 'p';

        $period = $this->periodModel->periodDatesResolve(
            $item,
            $dataSource['data']['groupByFilter'],
            $dataSource['data'][$periodFilterPrefix . 'eriodFromFilter'],
            $dataSource['data'][$periodFilterPrefix . 'eriodToFilter'],
            $isCompare
        );

        return $period;
    }

    /**
     * Retrieve period label
     *
     * @param array $periodDates
     * @param string $groupBy
     * @return string
     */
    private function getPeriodLabel($periodDates, $groupBy)
    {
        return $this->periodModel->getPeriodAsString($periodDates['start_date'], $periodDates['end_date'], $groupBy);
    }

    /**
     * Retrieve period url
     *
     * @param array $periodDates
     * @param array $comparePeriodDates
     * @param string $groupBy
     * @return string
     */
    private function getPeriodUrl($periodDates, $comparePeriodDates, $groupBy)
    {
        $dataProvider = $this->context->getDataProvider();
        $reportFrom = $this->getData('config/reportFrom') ?: '';
        $reportTo = $this->getData('config/reportTo') ?: '';
        $detailGroup = $this->getData('config/detailGroup') ?: false;

        $comparePeriodParams = [];
        $periodParams = [
            'period_from' => $periodDates['start_date']->format('Y-m-d'),
            'period_to'   => $periodDates['end_date']->format('Y-m-d'),
            'period_type' => PeriodSource::PERIOD_TYPE_CUSTOM
        ];

        if ($comparePeriodDates) {
            $comparePeriodParams = [
                'compare_from' => $comparePeriodDates['start_date']->format('Y-m-d'),
                'compare_to'   => $comparePeriodDates['end_date']->format('Y-m-d'),
                'compare_type' => PeriodSource::PERIOD_TYPE_CUSTOM
            ];
        }

        $params = array_merge($periodParams, $comparePeriodParams, $dataProvider->getAllowedRequestParams());

        return $this->urlModel->getUrlByPeriod(
            $reportFrom,
            $reportTo,
            $params,
            $detailGroup,
            $groupBy
        );
    }
}

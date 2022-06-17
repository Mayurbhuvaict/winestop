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





























namespace Aheadworks\AdvancedReports\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Class Url
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class Url
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve url for report
     *
     * @param string $report
     * @param string $reportTo
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param array $params
     * @return string
     */
    public function getUrl($report, $reportTo, $periodFrom, $periodTo, $params = [])
    {
        $params = array_merge(
            $params,
            [
                'period_from' => $periodFrom->format('Y-m-d'),
                'period_to'   => $periodTo->format('Y-m-d'),
                'period_type' => PeriodSource::PERIOD_TYPE_CUSTOM,
                'brc' => $this->getBrcParam($report, $reportTo)
            ]
        );

        return $this->urlBuilder->getUrl('advancedreports/' . $reportTo . '/index', ['_query' => $params]);
    }

    /**
     * Retrieve url for report by period
     *
     * @param string $reportFrom
     * @param string $reportTo
     * @param array $paramsFromRequest
     * @param bool $detailGroup
     * @param string $groupBy
     * @return string
     */
    public function getUrlByPeriod(
        $reportFrom,
        $reportTo,
        $paramsFromRequest = [],
        $detailGroup = true,
        $groupBy = GroupbySource::TYPE_MONTH
    ) {
        $params = [];
        switch ($groupBy) {
            case GroupbySource::TYPE_DAY:
                $params = [
                    'group_by' => GroupbySource::TYPE_DAY
                ];
                break;
            case GroupbySource::TYPE_WEEK:
                $params = [
                    'group_by' => $detailGroup ? GroupbySource::TYPE_DAY : $groupBy
                ];
                break;
            case GroupbySource::TYPE_MONTH:
                $params = [
                    'group_by' => $detailGroup ? GroupbySource::TYPE_WEEK : $groupBy
                ];
                break;
            case GroupbySource::TYPE_QUARTER:
                $params = [
                    'group_by' => $detailGroup ? GroupbySource::TYPE_MONTH : $groupBy
                ];
                break;
            case GroupbySource::TYPE_YEAR:
                $params = [
                    'group_by' => $detailGroup ? GroupbySource::TYPE_QUARTER : $groupBy
                ];
                break;
        }

        $params = array_merge(
            $params,
            $paramsFromRequest,
            [
                'brc' => $this->getBrcParam($reportFrom, $reportTo)
            ]
        );

        return $this->urlBuilder->getUrl('advancedreports/' . $reportTo . '/index', ['_query' => $params]);
    }

    /**
     * Retrieve brc param (for breadcrumbs)
     *
     * @param string $report
     * @param string $reportTo
     * @return string
     */
    private function getBrcParam($report, $reportTo)
    {
        $brc = $this->request->getParam('brc') ?: $report;
        return $brc . '-' . $reportTo;
    }
}

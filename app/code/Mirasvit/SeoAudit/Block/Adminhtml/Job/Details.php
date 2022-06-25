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


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Block\Adminhtml\Job;


use Magento\Framework\View\Element\Template;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Repository\JobRepository;

class Details extends Template
{
    const CHART_TYPE_DOUGHNUT = 'doughnut';
    const CHART_TYPE_BAR      = 'bar';

    const CHART_HEALTH_ID = 'healthScore';
    const CHART_ISSUE_ID  = 'issues';
    const CHART_JOBS_ID   = 'jobs';

    private $jobRepository;

    public function __construct(
        JobRepository $jobRepository,
        Template\Context $context,
        array $data = []
    ) {
        $this->jobRepository = $jobRepository;

        parent::__construct($context, $data);
    }

    public function getJob()
    {
        $id = $this->getRequest()->getParam(JobInterface::ID);

        $model = $this->jobRepository->get((int)$id);

        return $model;
    }

    public function getJobResult()
    {
        return $this->getJob()->getResult();
    }

    public function getErrorsTotal()
    {
        $resource = $this->jobRepository->getCollection()->getResource();
        $connection = $resource->getConnection();

        $query = "SELECT COUNT(check_id) as errors FROM {$resource->getTable(CheckResultInterface::TABLE_NAME)} WHERE job_id = {$this->getJob()->getId()} AND result < 0";

        $result = $connection->query($query)->fetchAll();

        return $result[0]['errors'];
    }

    public function getIssueLink(string $type): string
    {
        return $this->_urlBuilder->getUrl('*/*/url', [
            CheckResultInterface::JOB_ID => $this->getJob()->getId(),
            CheckResultInterface::RESULT => $type
        ]);
    }

    public function getChartConfigData(string $type, string $id): array
    {
        return [
            'id'           => $id,
            'type'         => $type,
            'data'         => $this->getChartDataConfig($type, $id),
            'chartOptions' => $this->getChartOptionsConfig($type, $id)
        ];
    }

    private function getChartDataConfig(string $type, string $id): array
    {
        $this->getChartData($id);

        $data    = [];
        $dataset = [
            'label'           => '',
            'backgroundColor' => $this->getBackgroundColors($id),
            'borderWidth'     => 0,
            'data'            => $this->getChartData($id),
        ];

        if ($id == self::CHART_JOBS_ID) {
            $job = $this->getJob();

            $borders = [];

            foreach ($dataset['data'] as $item) {
                $day = $item['x'];

                $borders[] = strrpos($job->getStartedAt(), $day) !== false ? 1000 : 0;
            }

            $dataset['borderWidth'] = $borders;
            $dataset['borderColor'] = 'rgba(255,255,255,0.5)';
        }

        $data['datasets'] = [$dataset];

        if ($id == self::CHART_ISSUE_ID) {
            $data['labels'] = ['Pages without errors', 'Pages with errors'];
        }

        return $data;
    }

    private function getBackgroundColors(string $id): array
    {
        switch ($id) {
            case self::CHART_ISSUE_ID:
                return ['#47cd4a', '#f44336'];
            default:
                return [];
        }
    }

    private function getChartData(string $id): array
    {
        switch ($id) {
            case self::CHART_ISSUE_ID:
                return $this->getErrorChartData()['rates'];
            case self::CHART_JOBS_ID:
                return $this->getJobsChartData();
            case self::CHART_HEALTH_ID:
                $score = $this->getJobResult()['score'];

                return [$score, 100 - $score];
            default:
                return [];
        }
    }

    public function getErrorChartData(): array
    {
        $job    = $this->getJob();
        $result = $job->getResult();

        $total         = isset($result['pages']) ? (int)$result['pages'] : 0;
        $withErrors    = isset($result['pages_error']) ? (int)$result['pages_error'] : 0;
        $withoutErrors = $total - $withErrors;

        $withoutErrorsRate = $withErrors ? round(100 * $withoutErrors / $total) : 100;

        $rates = [
            $withoutErrorsRate,
            100 - $withoutErrorsRate
        ];

        return [
            'withoutErrors' => $withoutErrors,
            'withErrors'    => $withErrors,
            'rates'         => $rates
        ];
    }

    private function getJobsChartData(): array
    {
        $data = [];

        $resource   = $this->jobRepository->getCollection()->getResource();
        $connection = $resource->getConnection();

        $lastJob = $this->jobRepository->getCollection()->getLastItem();

        $lastJobStartedAt = '';
        $timeLimit        = '';

        if ($lastJob) {
            $lastJobStartedAt = $lastJob->getStartedAt();

            $timeLimit = strtotime($lastJob->getStartedAt()) - 9 * 24 * 60 * 60;
            $timeLimit = date('Y-m-d', $timeLimit) . ' 00:00:00';

            $lastJobStartedAt = date('Y-m-d', strtotime($lastJob->getStartedAt())) . ' 23:59:59';
        } else {
            $lastJobStartedAt = date('Y-m-d', time()) . ' 23:59:59';

            $timeLimit = date('Y-m-d', time() - 9 * 24 * 60 * 60);
            $timeLimit .= ' 00:00:00';
        }

        $jobsQuery = "SELECT DATE(started_at) AS job_date, status, result_serialized
                    FROM {$resource->getTable(JobInterface::TABLE_NAME)}
                    WHERE started_at >= '{$timeLimit}' AND started_at <= '{$lastJobStartedAt}'";

        $result = $connection->query($jobsQuery)->fetchAll();

        $prepared = [];

        foreach ($result as $row) {
            $score = (array)SerializeService::decode($row['result_serialized']);
            $score = isset($score['score']) ? $score['score'] : 0;
            $date  = $row['job_date'];

            if (!isset($prepared[$date])) {
                $prepared[$date] = $score;

                continue;
            }

            $prepared[$date] = round(($prepared[$date] + $score) / 2);
        }

        foreach ($prepared as $d => $s) {
            $data[] = [
                'x' => $d,
                'y' => $s
            ];
        }

        return $data;
    }

    private function getChartOptionsConfig(string $type, string $id): array
    {
        $data = [];

        $data['title'] = [
            'display' => false,
            'text'    => '',
        ];

        $data['tooltips'] = [
            'enabled'   => true,
            'mode'      => 'nearest',
            'intersect' => false,
        ];

        if ($type === self::CHART_TYPE_DOUGHNUT) {
            $data['responsive']       = false;
            $data['cutoutPercentage'] = 60;
            $data['legend']           = ['display' => false];

            if ($id == self::CHART_HEALTH_ID) {
                $data['rotation']      = 0.85 * pi();
                $data['circumference'] = 1.3 * pi();
            }
        } else {
            $data['responsive'] = true;
            $data['maintainAspectRatio'] = false;
            $data['legend']     = [
                'display' => false,
            ];
            $data['scales']     = [
                'xAxes' => [
                    [
                        'type'      => 'time',
                        'display'   => true,
                        'gridLines' => [
                            'display' => false,
                        ],
                        'ticks'     => [
                            'fontSize'   => 12,
                            'fontStyle'  => 'bold',
                            'lineHeight' => 1,
                            'fontColor'  => '#666',
                        ],
                        'time'      => [
                            'unit'           => 'day',
                            'displayFormats' => [
                                'day' => 'MMM D',
                            ],
                            'stepSize'       => 1,
                        ],
                        'offset' => true
                    ],
                ],
                'yAxes' => [
                    [
                        'gridLines' => [
                            'tickMarkLength' => 6,
                            'zeroLineWidth'  => 2,
                        ],
                        'ticks'     => [
                            'stepSize'     => 25,
                            'beginAtZero'  => true,
                            'fontSize'     => 12,
                            'fontStyle'    => 'bold',
                            'fontFamily'   => "'Helvetica Neue', Helvetica, Arial, sans-serif",
                            'lineHeight'   => 1,
                            'fontColor'    => '#666',
                            'suggestedMax' => 100,
                        ],
                        'major'     => [
                            'enabled' => true,
                        ],
                    ],
                ],
            ];
        }

        return $data;
    }
}

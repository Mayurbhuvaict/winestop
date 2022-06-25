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


namespace Mirasvit\SeoAudit\Ui\Url\Listing;


use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Model\Config\Source\Check;
use Mirasvit\SeoAudit\Model\ResourceModel\Url\Collection;
use Mirasvit\SeoAudit\Repository\CheckResultRepository;
use Mirasvit\SeoAudit\Repository\UrlRepoitory;
use Mirasvit\SeoAudit\Service\CheckResultService;
use Mirasvit\SeoAudit\Service\UrlService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $checkSource;

    private $urlRepository;

    private $urlService;

    private $checkResultService;

    private $checkResultRepository;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Check                 $checkSourse,
        CheckResultRepository $checkResultRepository,
        CheckResultService    $checkResultService,
        UrlRepoitory          $urlRepoitory,
        UrlService            $urlService,
        string                $name,
        string                $primaryFieldName,
        string                $requestFieldName,
        ReportingInterface    $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface      $request,
        FilterBuilder         $filterBuilder,
        array                 $meta = [],
        array                 $data = []
    ) {
        $this->checkSource           = $checkSourse;
        $this->urlRepository         = $urlRepoitory;
        $this->urlService            = $urlService;
        $this->checkResultRepository = $checkResultRepository;
        $this->checkResultService    = $checkResultService;

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
    }

    protected function searchResultToOutput(SearchResultInterface $searchResult): array
    {
        $arrItems = [
            'totalRecords' => 0,
            'items'        => [],
        ];

        $jobId = $this->request->getParam(JobInterface::ID);

        $urlIds = [];

        foreach ($searchResult->getItems() as $item) {

            $id = (int)$item->getData(UrlInterface::ID);

            $urlIds[] = $id;

            $item->setData(
                'checks',
                $this->prepareChecks($item->getData('checks'))
            );

            $checkInstance = $this->checkResultRepository->getCheckInstanceByIdentifier(
                $item->getData(CheckResultInterface::IDENTIFIER)
            );

            $item->setData(
                CheckResultInterface::VALUE,
                $checkInstance->getValueGridOutput($item->getData(CheckResultInterface::VALUE))
            );

            $item->setData(
                CheckResultInterface::RESULT,
                $this->prepareCheckScoreHtml((int)$item->getData(CheckResultInterface::RESULT))
            );

            $item->setData(
                UrlInterface::URL,
                $this->prepareLinkHtml($item->getData(UrlInterface::URL))
            );

            $item->setData(
                'url_score',
                $this->getUrlHealthScoreHtml(
                    $this->checkResultService->getTotalScore((int)$jobId, $id)
                )
            );

            $item->setData(
                'all_checks',
                $this->prepareAllChecksHtml($this->getAllChecksData($id, (int)$jobId))
            );

            $arrItems['items'][] = $item->getData();
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        if (!count($urlIds)) {
            return $arrItems;
        }

        $resource   = $this->urlRepository->getCollection()->getResource();
        $connection = $resource->getConnection();
        $urlIds     = implode(',', $urlIds);

        $childQuery = "SELECT main_table.url_id as url_id, GROUP_CONCAT(t2.url_id) as linked
                    FROM {$resource->getTable(UrlInterface::TABLE_NAME)} as main_table
                    LEFT JOIN {$resource->getTable(UrlInterface::TABLE_NAME)} as t2
                    ON FIND_IN_SET(main_table.url_id, t2.parent_ids) AND main_table.url_id != t2.url_id
                    WHERE main_table.url_id IN ({$urlIds}) AND t2.job_id >= {$jobId} GROUP BY main_table.url_id";

        $linked = $connection->query($childQuery)->fetchAll();

        $linkedPrepared = [];

        foreach ($linked as $row) {
            $linkedPrepared[$row['url_id']] = $row['linked'] ? explode(',', $row['linked']) : [];
        }

        foreach ($arrItems['items'] as $idx => $item) {
            $childIds = isset($linkedPrepared[$item['url_id']]) ? $linkedPrepared[$item['url_id']] : [];
            $urls     = $this->urlRepository->getCollection()->addFieldToFilter(UrlInterface::ID, ['in' => $childIds]);
            $urls2    = clone $urls;

            $arrItems['items'][$idx]['linked_pages'] = $this->prepareLinkedHtml(
                $urls->addFieldToFilter(UrlInterface::TYPE, UrlInterface::TYPE_PAGE),
                'pages'
            );

            $arrItems['items'][$idx]['linked_resources'] = $this->prepareLinkedHtml(
                $urls2->addFieldToFilter(UrlInterface::TYPE, ['in' => $this->urlService->getResourcePageTypes()]),
                'resources'
            );
        }

        return $arrItems;
    }

    private function prepareLinkedHtml(Collection $collection, string $type): string
    {
        $html = "<h3>Linked {$type}</h3>";

        $headers = ['Status', 'URL'];

        $rows = [];
        /** @var UrlInterface $url */
        foreach ($collection as $url) {

            $statusFirstNumber = substr((string)$url->getStatusCode(), 0, 1);

            $cssClass = '';
            switch ($statusFirstNumber) {
                case 2:
                    $cssClass = 'success';
                    break;
                case 3:
                    $cssClass = 'warning';
                    break;
                case 4:
                case 5:
                    $cssClass = 'error';
                    break;
                default:
                    $cssClass = 'notice';
            }

            $rows[] = [
                '<div class="' . $cssClass . '">' . $url->getStatusCode() . '</div>',
                $this->prepareLinkHtml($url->getUrl()),
            ];
        }

        return $html . $this->makeTable($headers, $rows);
    }

    private function prepareChecks(string $checks): string
    {
        $headers = ['Check', 'Score'];
        $rows    = [];
        foreach (explode(',', $checks) as $check) {
            [$identifier, $result] = explode('|', $check);

            $rows[] = [
                $this->checkSource->getLabel($identifier),
                $this->prepareCheckScoreHtml((int)$result),
            ];
        }

        return $this->makeTable($headers, $rows);
    }

    private function getUrlHealthScoreHtml(int $score)
    {
        return '<h3>Health score: ' . $score . '</h3>';
    }

    private function prepareCheckScoreHtml(int $score): string
    {
        $grade = ceil(($score + 10) / 4); # -10..10 => 0..5

        if ($grade == 0) {
            $grade = 1;
        }

        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= '<div class="mst_seo_audit__score-grade _grade-' . $i . ' ' . ($i <= $grade ? '_active' : '') . '"><div></div></div>';
        }

        return '<div class="mst_seo_audit__score ' . ' _g-' . ($grade) . '">' . $html . '</div>';
    }

    private function prepareLinkHtml(string $url): string
    {
        return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
    }

    private function getAllChecksData(int $urlId, int $jobId): array
    {
        $result = [];

        $checks = $this->checkResultRepository->getCollection()
            ->addFieldToFilter(CheckResultInterface::URL_ID, $urlId)
            ->addFieldToFilter(CheckResultInterface::JOB_ID, $jobId);

        /** @var CheckResultInterface $check */
        foreach ($checks as $check) {
            $checkInstance = $this->checkResultRepository->getCheckInstanceByIdentifier($check->getIdentifier());

            $result[] = [
                'label'   => $checkInstance->getLabel(),
                'result'  => $this->prepareCheckScoreHtml($check->getResult()),
                'value'   => $checkInstance->getValueGridOutput($check->getValue()),
                'message' => $check->getMessage()
            ];
        }

        return $result;
    }

    private function prepareAllChecksHtml(array $checksData): string
    {
        $html = '<h3>Checks details</h3>';

        $headers = ['Check', 'Score', 'Message', 'Info'];
        $rows    = [];

        foreach ($checksData as $data) {
            $rows[] = [
                $data['label'],
                $data['result'],
                $data['message'],
                $data['value'],
            ];
        }

        $html .= $this->makeTable($headers, $rows);

        return $html;
    }

    private function makeTable(array $headers, array $rows): string
    {
        $table[] = '<table class="mst_seo_audit__check_details data-grid">';

        $table[] = '<thead><tr>';
        foreach ($headers as $header) {
            $table[] = '<th class="data-grid-th">' . $header . '</th>';
        }
        $table[] = '</tr></thead>';

        foreach ($rows as $idx => $row) {
            $cssClass = 'data-row' . ($idx % 2 ? '' : ' _odd-row');

            $table[] = '<tr class="' . $cssClass . '">';
            foreach ($row as $cell) {
                $table[] = '<td>' . $cell . '</td>';
            }
            $table[] = '</tr>';
        }

        $table[] = '</table>';

        return implode('', $table) . '<br><br>';
    }
}

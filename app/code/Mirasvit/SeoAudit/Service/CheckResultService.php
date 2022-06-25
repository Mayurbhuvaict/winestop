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


namespace Mirasvit\SeoAudit\Service;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Check\AbstractCheck;
use Mirasvit\SeoAudit\Model\ResourceModel\Url\Collection;
use Mirasvit\SeoAudit\Repository\CheckResultRepository;
use Mirasvit\SeoAudit\Repository\UrlRepoitory;

class CheckResultService
{
    private $checkResultRepository;

    private $urlRepository;

    private $urlService;

    public function __construct(
        CheckResultRepository $checkResultRepository,
        UrlRepoitory $urlRepoitory,
        UrlService $urlService
    ) {
        $this->checkResultRepository = $checkResultRepository;
        $this->urlRepository         = $urlRepoitory;
        $this->urlService            = $urlService;
    }

    public function agregateResult(JobInterface $job): array
    {
        $checked   = $this->getLinks($job);
        $pages     = $this->getLinks($job, [UrlInterface::TYPE_PAGE]);
        $resources = $this->getLinks($job, $this->urlService->getResourcePageTypes());

        $pagesWithErrors     = $this->getLinks($job, [UrlInterface::TYPE_PAGE], true);
        $resourcesWithErrors = $this->getLinks(
            $job,
            $this->urlService->getResourcePageTypes(),
            true
        );

        $score = $this->getTotalScore($job->getId());

        return [
            'score'           => $score,
            'error_total'     => $this->getIssueCount('error', $job->getId()),
            'warning_total'   => $this->getIssueCount('warning', $job->getId()),
            'notice_total'    => $this->getIssueCount('notice', $job->getId()),
            'crawled_total'   => count($checked),
            'pages'           => count($pages),
            'pages_error'     => count($pagesWithErrors),
            'resources'       => count($resources),
            'resources_error' => count($resourcesWithErrors)
        ];
    }

    private function getLinks(JobInterface $job, array $types = [], bool $hasError = false): array
    {
        $resource   = $this->urlRepository->getCollection()->getResource();
        $connection = $resource->getConnection();

        $query = "SELECT DISTINCT(url_id) AS url_id FROM {$resource->getTable(CheckResultInterface::TABLE_NAME)}
                    WHERE job_id = {$job->getId()}";

        if (count($types)) {
            $types = array_map(function ($type) {
                return '"' . $type . '"';
            }, $types);

            $typesString = implode(',', $types);

            $query .= "  AND url_type in ({$typesString})";
        }

        if ($hasError) {
            $query .= " AND result < 0";
        }

        return $connection->query($query)->fetchAll();
    }

    public function getTotalScore(int $jobId, int $urlId = null): int
    {
        $totalScore = 0;

        $resource   = $this->checkResultRepository->getCollection()->getResource();
        $connection = $resource->getConnection();

        $totalScoreQuery = "SELECT ROUND(100 * (SUM((10 + result) * importance)/COUNT(check_id)) / (SUM(20 * importance)/COUNT(check_id)), 0) AS score
                             FROM {$resource->getTable(CheckResultInterface::TABLE_NAME)}
                             WHERE url_type = 'page' AND job_id = {$jobId}";

        if ($urlId) {
            $totalScoreQuery .= " AND url_id = {$urlId}";
        }

        $result = $connection->query($totalScoreQuery)->fetchAll();

        return (int)$result[0]['score'];
    }

    private function getIssueCount(string $issueType, int $jobId): int
    {
        $condition = '';

        switch ($issueType) {
            case 'error':
                $condition = 'result < 0';
                break;
            case 'warning':
                $condition = 'result >= 0 AND result < 5';
                break;
            case 'notice':
                $condition = 'result >= 5 AND result < 9';
        }

        if (!$condition) {
            return 0;
        }
        
        $resource   = $this->checkResultRepository->getCollection()->getResource();
        $connection = $resource->getConnection();

        $query = "SELECT COUNT(check_id) as check_count FROM {$resource->getTable(CheckResultInterface::TABLE_NAME)}
                WHERE job_id = {$jobId} AND {$condition}";

        $result = $connection->query($query)->fetchAll();

        return (int)$result[0]['check_count'];
    }
}

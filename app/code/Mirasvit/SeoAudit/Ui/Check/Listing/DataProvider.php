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


namespace Mirasvit\SeoAudit\Ui\Check\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Repository\CheckResultRepository;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $urlBuilder;

    private $context;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->context    = $context;

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

    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [
            'totalRecords' => 0,
            'items' => [],
        ];

        foreach ($searchResult->getItems() as $item) {
            $this->prepareLinks($item);

            $arrItems['items'][] = $item->getData();
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }

    private function prepareLinks(DocumentInterface &$item)
    {
        $jobId = $this->context->getRequestParam(CheckResultInterface::JOB_ID, 0);

        $linkKeys = ['error', 'warning', 'notice'];

        foreach ($item->getData() as $key => $value) {
            if (!in_array($key, $linkKeys)) {
                continue;
            }

            $item->setData(
                $key,
                $this->prepareLinkHtml($item->getData('identifier'), (int)$jobId, $key, $value)
            );
        }
    }

    private function prepareLinkHtml(string $identifier, int $jobId, string $resultType, string $value = null): string
    {
        if (!$value) {
            $value = 0;
        }

        $url = $this->urlBuilder->getUrl('seoaudit/job/url', [
            CheckResultInterface::JOB_ID     => $jobId,
            CheckResultInterface::IDENTIFIER => $identifier,
            CheckResultInterface::RESULT     => $resultType
        ]);

        return '<a href="' . $url . '">' . $value . '</a>';
    }
}

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


namespace Mirasvit\SeoAudit\Ui\Job\Listing;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\SeoAudit\Api\Data\JobInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    protected function searchResultToOutput(SearchResultInterface $searchResult): array
    {
        $arrItems = [
            'totalRecords' => $searchResult->getTotalCount(),
            'items'        => [],
        ];

        /** @var JobInterface $item */
        foreach ($searchResult->getItems() as $item) {
            $item->setData('duration', $this->getDuration($item));

            $result = $item->getData('result_serialized');

            if ($result) {
                $result = SerializeService::decode($result);

                foreach ($result as $key => $value) {
                    $item->setData($key, $value);
                }
            }

            $arrItems['items'][] = $item->getData();
        }

        return $arrItems;
    }

    private function getDuration(DocumentInterface $job): string
    {
        /** @var JobInterface $job */
        if (!$job->getStartedAt()) {
            return '';
        }

        $from = strtotime($job->getStartedAt());

        $to = $job->getFinishedAt()
            ? strtotime($job->getFinishedAt())
            : time();

        $diff = $to - $from;

        $hours   = floor($diff / 3600);
        $minutes = floor(($diff - $hours * 3600) / 60);
        $seconds = $diff - $hours * 3600 - $minutes * 60;

        $duration = '';

        if ($hours) {
            $duration .= $hours . 'hrs ';
        }

        if ($minutes) {
            $duration .= $minutes . 'min ';
        }

        if (!$hours) {
            $duration .= $seconds . 's';
        }

        return $duration;
    }
}

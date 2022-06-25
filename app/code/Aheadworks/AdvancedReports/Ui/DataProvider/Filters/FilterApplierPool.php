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










































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Filters;

use Aheadworks\AdvancedReports\Model\Filter\FilterPool;
use Aheadworks\AdvancedReports\Ui\DataProvider\MetadataPool as DataProviderMetadataPool;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class FilterApplierPool
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Filters
 */
class FilterApplierPool
{
    /**
     * @var FilterPool
     */
    private $filterPool;

    /**
     * @var DataProviderMetadataPool
     */
    private $dataProviderMetadataPool;

    /**
     * @var FilterApplierInterface[]
     */
    private $appliers;

    /**
     * @param FilterPool $filterPool
     * @param DataProviderMetadataPool $dataProviderMetadataPool
     * @param array $appliers
     */
    public function __construct(
        FilterPool $filterPool,
        DataProviderMetadataPool $dataProviderMetadataPool,
        array $appliers = []
    ) {
        $this->filterPool = $filterPool;
        $this->dataProviderMetadataPool = $dataProviderMetadataPool;
        $this->appliers = $appliers;
    }

    /**
     * Apply default filters to report collection
     *
     * @param $collection
     * @param string $dataSourceName
     * @return void
     * @throws NotFoundException
     */
    public function applyFilters($collection, $dataSourceName)
    {
        $metadata = $this->dataProviderMetadataPool->getMetadata($dataSourceName);

        foreach ($metadata->getIndividualFilterAppliers() as $applierName) {
            $applier = $this->getApplierByName($applierName);
            $applier->apply($collection, $this->getFilterPool());
        }
        foreach ($metadata->getDefaultFilterAppliers() as $applierName) {
            $applier = $this->getApplierByName($applierName);
            $applier->apply($collection, $this->getFilterPool());
        }
    }

    /**
     * Retrieve filter pool
     *
     * @return FilterPool
     */
    public function getFilterPool()
    {
        return $this->filterPool;
    }

    /**
     * Retrieve applier by name
     *
     * @param string $applierName
     * @return FilterApplierInterface
     * @throws NotFoundException
     */
    private function getApplierByName($applierName)
    {
        if (!isset($this->appliers[$applierName])) {
            throw new NotFoundException(__('Unknown applier: %s requested', $applierName));
        }

        return $this->appliers[$applierName];
    }
}

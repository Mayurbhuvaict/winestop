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
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;

/**
 * Class DashboardDataProvider
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class DashboardDataProvider implements DataProviderInterface
{
    /**
     * Data Provider name
     *
     * @var string
     */
    protected $name;

    /**
     * Data Provider Primary Identifier name
     *
     * @var string
     */
    protected $primaryFieldName;

    /**
     * Data Provider Request Parameter Identifier name
     *
     * @var string
     */
    protected $requestFieldName;

    /**
     * @var array
     */
    private $reports = [];

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * Provider configuration data
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var UiComponentInterface[]
     */
    private $reportComponents = [];

    /**
     * @var UiComponentFactory
     */
    private $factory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param UiComponentFactory $factory
     * @param ObjectManagerInterface $objectManager
     * @param array $reports
     * @param array $meta
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        UiComponentFactory $factory,
        ObjectManagerInterface $objectManager,
        array $reports = [],
        array $meta = [],
        array $data = []
    ) {
        $this->name = $name;
        $this->primaryFieldName = $primaryFieldName;
        $this->requestFieldName = $requestFieldName;
        $this->factory = $factory;
        $this->objectManager = $objectManager;
        $this->reports = $reports;
        $this->meta = $meta;
        $this->data = $data;
        $this->createReportComponents();
    }

    /**
     * Get Data Provider name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get primary field name
     *
     * @return string
     */
    public function getPrimaryFieldName()
    {
        return $this->primaryFieldName;
    }

    /**
     * Get field name in request
     *
     * @return string
     */
    public function getRequestFieldName()
    {
        return $this->requestFieldName;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Get field Set meta info
     *
     * @param string $fieldSetName
     * @return array
     */
    public function getFieldSetMetaInfo($fieldSetName)
    {
        return isset($this->meta[$fieldSetName]) ? $this->meta[$fieldSetName] : [];
    }

    /**
     * @param string $fieldSetName
     * @return array
     */
    public function getFieldsMetaInfo($fieldSetName)
    {
        return isset($this->meta[$fieldSetName]['children']) ? $this->meta[$fieldSetName]['children'] : [];
    }

    /**
     * @param string $fieldSetName
     * @param string $fieldName
     * @return array
     */
    public function getFieldMetaInfo($fieldSetName, $fieldName)
    {
        return isset($this->meta[$fieldSetName]['children'][$fieldName])
            ? $this->meta[$fieldSetName]['children'][$fieldName]
            : [];
    }

    /**
     * {@inheritdoc}
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
    }

    /**
     * Returns search criteria
     *
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Returns SearchResult
     *
     * @return null
     */
    public function getSearchResult()
    {
        return null;
    }

    /**
     * self::setOrder() alias
     *
     * @param string $field
     * @param string $direction
     * @return void
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock
     */
    public function addOrder($field, $direction)
    {
    }

    /**
     * Set Query limit
     *
     * @param int $offset
     * @param int $size
     * @return void
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock
     */
    public function setLimit($offset, $size)
    {
    }

    /**
     * Get config data
     *
     * @return array
     */
    public function getConfigData()
    {
        return isset($this->data['config']) ? $this->data['config'] : [];
    }

    /**
     * Set data
     *
     * @param mixed $config
     * @return void
     */
    public function setConfigData($config)
    {
        $this->data['config'] = $config;
    }

    /**
     * Retrieve default filter pool
     *
     * @return FilterPool
     */
    public function getDefaultFilterPool()
    {
        $firstReport = reset($this->reportComponents);

        return $firstReport->getContext()->getDataProvider()->getDefaultFilterPool();
    }

    /**
     * Retrieve data
     *
     * @return array
     */
    public function getData()
    {
        $data['reports'] = $this->getReportsData();
        $data['priceFormat'] = $this->getPriceFormat($data['reports']);

        return $data;
    }

    /**
     * Create report components
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createReportComponents()
    {
        foreach ($this->reports as $name => $report) {
            $this->reportComponents[$name] = $this->createReportComponent($report);
        }
    }

    /**
     * Retrieve reports data
     *
     * @return array
     */
    private function getReportsData()
    {
        $data = [];
        foreach ($this->reportComponents as $name => $component) {
            $reportData = $component->getContext()->getDataSourceData($component);
            $reportData = reset($reportData);
            $data[$name] = isset($reportData['config']['data']) ? $reportData['config']['data'] : [];
        }

        return $data;
    }

    /**
     * Retrieve price format from first report in array
     *
     * @param array $reportsData
     * @return array
     */
    private function getPriceFormat($reportsData)
    {
        $firstReport = reset($reportsData);

        return $firstReport['priceFormat'];
    }

    /**
     * Create report component
     *
     * @param string $report
     * @return UiComponentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createReportComponent($report)
    {
        $component = $this->factory->create($report);
        $dataProvider = $this->createDashboardDataProviderForReport($component);
        $component->getContext()->setDataProvider($dataProvider);
        $this->prepareComponent($component);

        return $component;
    }

    /**
     * Create dashboard data provider for report
     *
     * @param UiComponentInterface $component
     * @return \Aheadworks\AdvancedReports\Ui\DataProvider\DataProvider
     */
    private function createDashboardDataProviderForReport($component)
    {
        /** @var \Aheadworks\AdvancedReports\Ui\DataProvider\DataProvider $dataProvider */
        $dataProvider = $component->getContext()->getDataProvider();
        $arguments = [
            'name' => $dataProvider->getName(),
            'requestFieldName' => $dataProvider->getRequestFieldName(),
            'primaryFieldName' => $dataProvider->getPrimaryFieldName(),
            'data' => $dataProvider->getProviderData()
        ];

        return $this->objectManager->create('AwArepDataProviderForDashboard', $arguments);
    }

    /**
     * Call prepare method in the component UI
     *
     * @param UiComponentInterface $component
     * @return void
     */
    private function prepareComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $child) {
            $this->prepareComponent($child);
        }

        $component->prepare();
    }
}

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



namespace Mirasvit\SeoContent\Model\ResourceModel\Template;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;

class Grid extends SearchResult
{
    /**
     * @var string
     */
    protected $document = \Mirasvit\SeoContent\Model\Template::class;

    /**
     * Grid constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = TemplateInterface::TABLE_NAME,
        $resourceModel = \Mirasvit\SeoContent\Model\ResourceModel\Template::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
    //    /**
    //     * @var AggregationInterface
    //     */
    //    protected $aggregations;
    //
    //    /**
    //     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
    //     */
    //    public function __construct(
    //        \Magento\Store\Model\StoreManagerInterface $storeManager,
    //        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
    //        \Psr\Log\LoggerInterface $logger,
    //        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
    //        \Magento\Framework\Event\ManagerInterface $eventManager,
    //        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
    //        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
    //        $mainTable = TemplateInterface::TABLE_NAME,
    //        $eventPrefix = 'seo_template_grid_collection',
    //        $eventObject = 'template_grid_collection',
    //        $resourceModel = 'Mirasvit\SeoContent\Model\ResourceModel\Template',
    //        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document'
    //    ) {
    //        parent::__construct(
    //            $storeManager,
    //            $entityFactory,
    //            $logger,
    //            $fetchStrategy,
    //            $eventManager,
    //            $connection,
    //            $resource
    //        );
    //        $this->_eventPrefix = $eventPrefix;
    //        $this->_eventObject = $eventObject;
    //        $this->_init($model, $resourceModel);
    //        $this->setMainTable($mainTable);
    //    }
    //
    //    /**
    //     * @return AggregationInterface
    //     */
    //    public function getAggregations()
    //    {
    //        return $this->aggregations;
    //    }
    //
    //    /**
    //     * @param AggregationInterface $aggregations
    //     *
    //     * @return void
    //     */
    //    public function setAggregations($aggregations)
    //    {
    //        $this->aggregations = $aggregations;
    //    }
    //
    //    /**
    //     * Get search criteria.
    //     *
    //     * @return \Magento\Framework\Api\SearchCriteriaInterface|bool
    //     */
    //    public function getSearchCriteria()
    //    {
    //        return false;
    //    }
    //
    //    /**
    //     * Set search criteria.
    //     *
    //     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    //     *
    //     * @return $this
    //     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    //     */
    //    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    //    {
    //        return $this;
    //    }
    //
    //    /**
    //     * Get total count.
    //     *
    //     * @return int
    //     */
    //    public function getTotalCount()
    //    {
    //        return $this->getSize();
    //    }
    //
    //    /**
    //     * Set total count.
    //     *
    //     * @param int $totalCount
    //     *
    //     * @return $this
    //     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    //     */
    //    public function setTotalCount($totalCount)
    //    {
    //        return $this;
    //    }
    //
    //    /**
    //     * Set items list.
    //     *
    //     * @param \Magento\Framework\Api\ExtensibleDataInterface[]|array $items
    //     *
    //     * @return $this
    //     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    //     */
    //    public function setItems(array $items = null)
    //    {
    //        return $this;
    //    }
    //
    //    /**
    //     * {@inheritdoc}
    //     */
    //    protected function _renderFiltersBefore()
    //    {
    //        $this->addStoreColumn();
    //        parent::_renderFiltersBefore();
    //    }
}

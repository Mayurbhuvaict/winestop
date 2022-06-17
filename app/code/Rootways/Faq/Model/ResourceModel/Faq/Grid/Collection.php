<?php
/**
 * FAQ model for collect data.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Model\ResourceModel\Faq\Grid;

class Collection extends \Rootways\Faq\Model\ResourceModel\Faq\Collection implements \Magento\Framework\Api\Search\SearchResultInterface
{
    protected $_aggregations;
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable = 'rootways_faq',
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }
	
    public function getAggregations()
    {
        return $this->_aggregations;
    }
	
    public function setAggregations($aggregations)
    {
        $this->_aggregations = $aggregations;
    }
	
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }
	
    public function getSearchCriteria()
    {
        return null;
    }
	
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }
	
    public function getTotalCount()
    {
        return $this->getSize();
    }
	
    public function setTotalCount($totalCount)
    {
        return $this;
    }
	
    public function setItems(array $items = null)
    {
        return $this;
    }
	
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->joinLeft(
            ['faq_store' => $this->getTable('rootways_faq_store')],
            'main_table.faq_id = faq_store.faq_id',
            ['store_id']
        );
        $this->getSelect()->group(
            'main_table.faq_id'
        );
        parent::_renderFiltersBefore();
    }
}

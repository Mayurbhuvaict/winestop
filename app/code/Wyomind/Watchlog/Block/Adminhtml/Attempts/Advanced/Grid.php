<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Block\Adminhtml\Attempts\Advanced;

/**
 * Grid block showing the aggregated date from the connection attempts
 * For each IP : get the number of failure and success
 * @author Wyomind
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory
     */
    protected $_attemptsCollectionFactory;
    public function __construct(\Wyomind\Watchlog\Helper\Delegate $wyomind, \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory $attemptsCollectionFactory, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_attemptsCollectionFactory = $attemptsCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    /**
     * Initializer
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('attemptsGrid');
        $this->setDefaultSort('failed');
        $this->setDefaultDir('DESC');
    }
    /**
     * Prepare collection for the grid
     * @return \Magento\Backend\Block\Widget\Grid
     */
    public function _prepareCollection()
    {
        $collection = $this->_attemptsCollectionFactory->create()->getHistory();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * Prepare columns for the grid
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('ip', ['header' => __('IP'), 'index' => 'ip', 'renderer' => '\\Wyomind\\Watchlog\\Block\\Adminhtml\\Attempts\\Renderer\\Ip']);
        $this->addColumn('date', ['header' => __('Last Attempts'), 'index' => 'date', 'type' => 'datetime']);
        $this->addColumn('attempts', ['header' => __('Attempts'), 'index' => 'attempts']);
        $this->addColumn('failed', ['header' => __('Failed'), 'index' => 'failed']);
        $this->addColumn('succeeded', ['header' => __('Succeeded'), 'index' => 'succeeded']);
        return parent::_prepareColumns();
    }
    /**
     * Set the action when a row is clicked
     * (in this case, nothing must happen)
     * @param \Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
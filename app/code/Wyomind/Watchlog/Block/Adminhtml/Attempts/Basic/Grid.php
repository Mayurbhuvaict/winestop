<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Block\Adminhtml\Attempts\Basic;

/**
 * Grid definition
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory $collectionFactory, array $data = [])
    {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
    /**
     * initializer
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('attemptsGrid');
        $this->setDefaultSort('date');
        $this->setDefaultDir('DESC');
    }
    /**
     * Prepare collection
     * @return \Magento\Backend\Block\Widget\Grid
     */
    public function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * Prepare columns
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('ip', ['header' => __('IP'), 'index' => 'ip', 'renderer' => '\\Wyomind\\Watchlog\\Block\\Adminhtml\\Attempts\\Renderer\\Ip']);
        $this->addColumn('date', ['header' => __('Date'), 'index' => 'date', 'type' => 'datetime']);
        $this->addColumn('login', ['header' => __('Login'), 'index' => 'login']);
        $this->addColumn('message', ['header' => __('Message'), 'index' => 'message']);
        $this->addColumn('url', ['header' => __('Url'), 'index' => 'url']);
        $this->addColumn('status', ['header' => __('Status'), 'index' => 'status', 'renderer' => '\\Wyomind\\Watchlog\\Block\\Adminhtml\\Attempts\\Renderer\\Status']);
        return parent::_prepareColumns();
    }
    /**
     * Row click url
     * @param \Magento\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
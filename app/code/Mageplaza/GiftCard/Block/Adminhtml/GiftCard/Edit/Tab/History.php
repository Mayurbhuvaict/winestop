<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History\DetailRenderer;
use Mageplaza\GiftCard\Block\Adminhtml\Grid\Column\Renderer\Price;
use Mageplaza\GiftCard\Model\GiftCard\Action;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\ResourceModel\History\CollectionFactory;

/**
 * Class Information
 * @package Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab
 */
class History extends Extended implements TabInterface
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * History constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param Registry $coreRegistry
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $coreRegistry,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry      = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('giftcard_history_grid');
        $this->setDefaultSort('history_created_at');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $giftCard   = $this->_coreRegistry->registry('current_giftcard');
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('giftcard_id', $giftCard->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_created_at', [
            'header' => __('Created At'),
            'index'  => 'created_at',
            'type'   => 'datetime'
        ]);
        $this->addColumn('history_action', [
            'header'  => __('Action'),
            'align'   => 'center',
            'index'   => 'action',
            'type'    => 'options',
            'options' => Action::getOptionArray()
        ]);
        $this->addColumn('history_balance', [
            'header'   => __('Balance'),
            'align'    => 'right',
            'index'    => 'balance',
            'type'     => 'price',
            'renderer' => Price::class
        ]);

        $this->addColumn('history_amount', [
            'header'   => __('Amount Change'),
            'align'    => 'right',
            'index'    => 'amount',
            'type'     => 'price',
            'renderer' => Price::class
        ]);
        $this->addColumn('history_status', [
            'header'  => __('Status'),
            'align'   => 'center',
            'index'   => 'status',
            'type'    => 'options',
            'options' => Status::getOptionArray()
        ]);
        $this->addColumn('history_extra_content', [
            'header'   => 'Action Detail',
            'filter'   => false,
            'sortable' => false,
            'renderer' => DetailRenderer::class
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('mpgiftcard/code/history', ['_current' => true]);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('History');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('History');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}

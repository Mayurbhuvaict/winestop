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

namespace Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab\Generate;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Block\Adminhtml\Grid\Column\Renderer\Price;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Mageplaza\GiftCard\Model\Source\Status;

/**
 * Class Grid
 * @package Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab\Generate
 */
class Grid extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Registry $registry,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry      = $registry;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('poolCodesGrid');
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $pool       = $this->_coreRegistry->registry('current_pool');
        $collection = $this->_collectionFactory->create();
        if ($pool->getId()) {
            $collection->addFieldToFilter('pool_id', ['eq' => $pool->getId()]);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('pool_code', [
            'header' => __('Code'),
            'index'  => 'code',
            'type'   => 'text'
        ]);

        $this->addColumn('pool_balance', [
            'header'   => __('Balance'),
            'align'    => 'right',
            'index'    => 'balance',
            'type'     => 'price',
            'renderer' => Price::class
        ]);

        $this->addColumn('pool_status', [
            'header'  => __('Status'),
            'index'   => 'status',
            'type'    => 'options',
            'options' => Status::getOptionArray()
        ]);

        $this->addColumn('pool_created_at', [
            'header'           => __('Created Date'),
            'type'             => 'datetime',
            'index'            => 'created_at',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
        ]);

        $this->addExportType('*/*/exportCouponsCsv', __('CSV'));
        $this->addExportType('*/*/exportCouponsXml', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Configure grid mass actions
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('giftcard_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseAjax(true);
        $this->getMassactionBlock()->setHideFormElement(true);

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label'    => __('Delete'),
                'url'      => $this->getUrl('*/*/cardsMassDelete', ['_current' => true]),
                'confirm'  => __('Are you sure you want to delete the selected code(s)?'),
                'complete' => 'poolCodesGridJsObject.reload()'
            ]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}

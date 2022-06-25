<?php

namespace Meetanshi\ShippingRates\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context as Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Meetanshi\ShippingRates\Model\ResourceModel\Rate\CollectionFactory;
use Meetanshi\ShippingRates\Helper\Data;

class Rates extends Extended
{
    protected $rateCollection;
    protected $helper;

    public function __construct(CollectionFactory $rateCollection, Data $helper, Context $context, BackendHelper $backendHelper)
    {
        $this->rateCollection = $rateCollection;
        $this->helper = $helper;

        parent::__construct($context, $backendHelper);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('shippingratesRates');
        $this->setUseAjax(true);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('shippingrates/rate/edit', ['id' => $row->getId()]);
    }

    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = $this->rateCollection->create()->addFieldToFilter('method_id', $id);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('country', [
            'header' => __('Country'),
            'index' => 'country',
            'type' => 'options',
            'options' => $this->helper->getCountries(),
        ]);

        $this->addColumn('state', [
            'header' => __('State'),
            'index' => 'state',
            'type' => 'options',
            'options' => $this->helper->getStates(),
        ]);

        $this->addColumn('city', [
            'header' => __('City'),
            'index' => 'city',
        ]);

        $this->addColumn('zip_from', [
            'header' => __('Zip From'),
            'index' => 'zip_from',
        ]);

        $this->addColumn('zip_to', [
            'header' => __('Zip To'),
            'index' => 'zip_to',
        ]);

        $this->addColumn('price_from', [
            'header' => __('Price From'),
            'index' => 'price_from',
        ]);

        $this->addColumn('price_to', [
            'header' => __('Price To'),
            'index' => 'price_to',
        ]);

        $this->addColumn('weight_from', [
            'header' => __('Weight From'),
            'index' => 'weight_from',
        ]);

        $this->addColumn('weight_to', [
            'header' => __('Weight To'),
            'index' => 'weight_to',
        ]);

        $this->addColumn('qty_from', [
            'header' => __('Qty From'),
            'index' => 'qty_from',
        ]);

        $this->addColumn('qty_to', [
            'header' => __('Qty To'),
            'index' => 'qty_to',
        ]);

        $this->addColumn('shipping_type', [
            'header' => __('Shipping Type'),
            'index' => 'shipping_type',
            'type' => 'options',
            'options' => $this->helper->getShippingType(),
        ]);

        $this->addColumn('cost_base', [
            'header' => __('Rate'),
            'index' => 'cost_base',
        ]);

        $this->addColumn('cost_percent', [
            'header' => __('PPP'),
            'index' => 'cost_percent',
        ]);

        $this->addColumn('cost_product', [
            'header' => __('FRPP'),
            'index' => 'cost_product',
        ]);

        $this->addColumn('cost_weight', [
            'header' => __('FRPUW'),
            'index' => 'cost_weight',
        ]);

        $this->addColumn('time_delivery', [
            'header' => __('Estimated Delivery (days)'),
            'index' => 'time_delivery',
        ]);

        $this->addColumn('action', [
            'header' => __('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('Delete'),
                    'url' => ['base' => '*/*/delete'],
                    'field' => 'id'
                ]
            ],
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ]);

        return parent::_prepareColumns();
    }
}

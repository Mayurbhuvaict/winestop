<?php

namespace Magecomp\Extrafee\Block\Adminhtml\Category;

use Magento\Backend\Block\Widget\Context as Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Meetanshi\ShippingRates\Helper\Data;
use Magecomp\Extrafee\Model\ResourceModel\Category\CollectionFactory;

class Grid extends Extended
{
    protected $categoryCollection;
    protected $helper;

    public function __construct(CollectionFactory $categoryCollection, Context $context, BackendHelper $backendHelper, Data $helper)
    {
        $this->categoryCollection = $categoryCollection;
        $this->helper = $helper;

        parent::__construct($context, $backendHelper);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('CategoryGrid');
        $this->setDefaultSort('position');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('shippingcategory/category/edit', ['id' => $row->getId()]);
    }

    protected function _prepareCollection()
    {
        $collection = $this->categoryCollection->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = $this->helper;
        $this->addColumn('id', [
            'header' => __('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ]);

        $this->addColumn('category_name', [
            'header' => __('Name'),
            'index' => 'category_name',
        ]);

        $this->addColumn('position', [
            'header' => __('Position'),
            'index' => 'position',
        ]);

        $this->addColumn('is_active', [
            'header' => __('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'is_active',
            'type' => 'options',
            'options' => $hlp->getStatuses(),
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('category');

        $actions = [
            'massActivate' => 'Activate',
            'massInactivate' => 'Inactivate',
            'massDelete' => 'Delete',
        ];

        foreach ($actions as $code => $label) {
            $this->getMassactionBlock()->addItem($code, [
                'label' => __($label),
                'url' => $this->getUrl('*/*/' . $code),
                'confirm' => ($code == 'massDelete' ? __('Are you sure?') : null),
            ]);
        }
        return $this;
    }
}

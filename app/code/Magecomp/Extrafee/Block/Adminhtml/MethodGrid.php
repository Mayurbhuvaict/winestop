<?php

namespace Magecomp\Extrafee\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context as Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Meetanshi\ShippingRates\Helper\Data;
use Meetanshi\ShippingRates\Model\ResourceModel\Method\CollectionFactory;
use Magecomp\Extrafee\Model\CategoryFactory;

class MethodGrid extends \Meetanshi\ShippingRates\Block\Adminhtml\Method\Grid
{
    protected $methodCollection;
    protected $helper;
    protected $categoryFactory;
    protected $categoryOptions;

    public function __construct(CollectionFactory $methodCollection, Data $helper, Context $context, BackendHelper $backendHelper, CategoryFactory $categoryFactory)
    {
        $this->methodCollection = $methodCollection;
        $this->helper = $helper;
        $this->categoryFactory = $categoryFactory;

        parent::__construct($methodCollection, $helper, $context, $backendHelper);
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        if(!$this->categoryOptions){

            $categories = $this->categoryFactory->create()->getCollection();
            $options = [];
            foreach ($categories as $key => $_cat) {
                $options[$_cat->getId()] =$_cat->getCategoryName();
            }
            $this->categoryOptions = $options;
        }
        $this->addColumn('category_id', [
            'header' => __('Category name'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'category_id',
            'type' => 'options',
            'options' => $this->categoryOptions,
        ]);

        return $this;
    }
}

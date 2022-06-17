<?php

namespace Meetanshi\ShippingRates\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Method extends Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_method';
        $this->_blockGroup = 'Meetanshi_ShippingRates';
        $this->_headerText = __('Methods');
        $this->_addButtonLabel = __('Add Method');
        parent::_construct();
    }
}

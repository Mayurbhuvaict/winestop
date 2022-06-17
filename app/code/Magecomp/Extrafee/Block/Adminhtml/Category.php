<?php

namespace Magecomp\Extrafee\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Category extends Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Magecomp_Extrafee';
        $this->_headerText = __('Category');
        $this->_addButtonLabel = __('Add Category');
        parent::_construct();
    }
}

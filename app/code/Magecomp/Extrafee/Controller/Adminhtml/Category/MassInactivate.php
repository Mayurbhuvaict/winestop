<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magecomp\Extrafee\Controller\Adminhtml\Category;

class MassInactivate extends Category
{
    public function execute()
    {
        return $this->_modifyStatus(0);
    }
}

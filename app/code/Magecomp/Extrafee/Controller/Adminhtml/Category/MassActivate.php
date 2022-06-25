<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magecomp\Extrafee\Controller\Adminhtml\Category;

class MassActivate extends Category
{
    public function execute()
    {
        return $this->_modifyStatus(1);
    }
}

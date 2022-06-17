<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magecomp\Extrafee\Controller\Adminhtml\Category;

class NewAction extends Category
{
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}

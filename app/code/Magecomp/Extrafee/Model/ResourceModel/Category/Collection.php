<?php

namespace Magecomp\Extrafee\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Magecomp\Extrafee\Model\Category', 'Magecomp\Extrafee\Model\ResourceModel\Category');
    }
}

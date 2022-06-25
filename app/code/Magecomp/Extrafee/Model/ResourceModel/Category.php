<?php
namespace Magecomp\Extrafee\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Category extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('shippingrate_category', 'id');
    }
}

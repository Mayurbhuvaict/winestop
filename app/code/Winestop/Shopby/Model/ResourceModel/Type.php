<?php

namespace Winestop\Shopby\Model\ResourceModel;

class Type extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('winestop_shopby_type', 'type_id');
    }
}

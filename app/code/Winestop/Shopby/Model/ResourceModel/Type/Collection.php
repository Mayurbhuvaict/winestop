<?php

namespace Winestop\Shopby\Model\ResourceModel\Type;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'type_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Winestop\Shopby\Model\Type',
            'Winestop\Shopby\Model\ResourceModel\Type'
        );
    }
}
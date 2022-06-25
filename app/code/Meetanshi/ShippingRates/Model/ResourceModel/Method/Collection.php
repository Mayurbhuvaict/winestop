<?php

namespace Meetanshi\ShippingRates\Model\ResourceModel\Method;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Meetanshi\ShippingRates\Model\Method', 'Meetanshi\ShippingRates\Model\ResourceModel\Method');
    }

    public function addStoreFilter($storeId)
    {
        $storeId = (int)$storeId;
        $this->getSelect()->where('stores="" OR stores LIKE "%,' . $storeId . ',%"');

        return $this;
    }

    public function addCustomerGroupFilter($groupId)
    {
        $groupId = (int)$groupId;
        $this->getSelect()->where('cust_groups="" OR cust_groups LIKE "%,' . $groupId . ',%"');

        return $this;
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('method_id', 'comment');
    }
}

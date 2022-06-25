<?php

namespace Winestop\Testimonials\Model\ResourceModel\Testimonials;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Winestop\Testimonials\Model\Testimonials', 'Winestop\Testimonials\Model\ResourceModel\Testimonials');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>
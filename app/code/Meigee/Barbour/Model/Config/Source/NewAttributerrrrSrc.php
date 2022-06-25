<?php


namespace Meigee\Barbour\Model\Config\Source;

class NewAttributerrrrSrc implements \Magento\Framework\Option\ArrayInterface
{
//    protected $_entityType;
//    protected $_store;
//
//    public function __construct(
//        \Magento\Store\Model\Store $store,
//        \Magento\Eav\Model\Entity\Type $entityType
//    ) {
//        $this->_store = $store;
//        $this->_entityType = $entityType;
//    }

    public function toOptionArray()
    {

        return [
                      ['value' => 1, 'title' => __('title 1'), 'img' => 'Meigee_Barbour::images/default-store.jpeg']
                    , ['value' => 2, 'title' => __('title 2'), 'img' => 'Meigee_Barbour::images/default-store.jpeg']
                    , ['value' => 3, 'title' => __('title 3'), 'img' => 'Meigee_Barbour::images/default-store.jpeg']
                    , ['value' => 4, 'title' => __('title 4'), 'img' => 'Meigee_Barbour::images/default-store.jpeg']
                ];




    }

}

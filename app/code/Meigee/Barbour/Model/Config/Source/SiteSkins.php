<?php
namespace Meigee\Barbour\Model\Config\Source;

class SiteSkins implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'default.css', 'label' => __('Default'), 'img' => 'Meigee_Barbour::images/default.jpg', 'header' => 'default'],
            ['value' => 'perfumes.css', 'label' => __('Perfumes'), 'img' => 'Meigee_Barbour::images/perfumes.jpg'],
            ['value' => 'electronics_1.css', 'label' => __('Electronics 1'), 'img' => 'Meigee_Barbour::images/electronics_1.jpg'],
            ['value' => 'electronics_2.css', 'label' => __('Electronics 2'), 'img' => 'Meigee_Barbour::images/electronics_2.jpg'],
            ['value' => 'cloth.css', 'label' => __('Cloth'), 'img' => 'Meigee_Barbour::images/cloth.jpg'],
            ['value' => 'jewelry.css', 'label' => __('Jewelry'), 'img' => 'Meigee_Barbour::images/jewelry.jpg'],
            ['value' => 'handmade.css', 'label' => __('Handmade'), 'img' => 'Meigee_Barbour::images/handmade.jpg'],
            ['value' => 'cloth_2.css', 'label' => __('Cloth 2'), 'img' => 'Meigee_Barbour::images/cloth_2.jpg'],
            ['value' => 'cloth_3.css', 'label' => __('Cloth 3'), 'img' => 'Meigee_Barbour::images/cloth_3.jpg'],
        ];
  }
}

<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Helper;

/**
 * Attributes management
 */
class AttributesImages extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Framework\App\Helper\Context $context)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context);
    }
    /**
     * {image} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string product's image
     */
    public function imageLink($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        $idCol = $this->moduleHelper->moduleIsEnabled('Magento_Enterprise') ? 'row_id' : 'entity_id';
        if ($item == null) {
            return '';
        }
        $baseImage = $item->getImage();
        $value = '';
        $role = $options['role'] ?? false;
        if ($role) {
            $exploded = explode('_', $role);
            $method = "";
            foreach ($exploded as $x) {
                $method .= ucfirst(strtolower($x));
            }
            $methodName = 'get' . str_replace([' ', '_'], '', ucfirst(trim($method)));
            $image = $item->{$methodName}();
            if ($image == "") {
                $image = $item->getData($role);
            }
            if ($image != "") {
                $path = 'catalog/product' . $image;
                $value = $model->baseImg . str_replace('//', '/', $path);
            }
        } elseif (!isset($options['index']) || $options['index'] == 0) {
            if ($item->getImage() != null && $item->getImage() != "" && $item->getImage() != 'no_selection') {
                $path = 'catalog/product/' . $item->getImage();
                $value = $model->baseImg . str_replace('//', '/', $path);
            } else {
                if ($model->defaultImage != '') {
                    $value = $model->baseImg . '/catalog/product/placeholder/' . $model->defaultImage;
                }
            }
        } elseif (isset($model->gallery[$item->getData($idCol)]['src'][$options['index'] - 1]) && $options['index'] > 0) {
            if ($model->gallery[$item->getData($idCol)]['src'][$options['index'] - 1] != $baseImage) {
                $path = 'catalog/product/' . $model->gallery[$item->getData($idCol)]['src'][$options['index'] - 1];
                $value = $model->baseImg . str_replace('//', '/', $path);
            }
        } elseif ($options['index'] < 0) {
            $reversedImages = array_reverse($model->gallery[$item->getData($idCol)]['src']);
            $index = $options['index'] * -1 - 1;
            if (isset($reversedImages[$index])) {
                $path = 'catalog/product/' . $reversedImages[$index];
                $value = $model->baseImg . str_replace('//', '/', $path);
            }
        }
        return $value;
    }
}
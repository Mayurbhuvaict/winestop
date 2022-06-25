<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Helper;

/**
 * Attributes management
 */
class AttributesStockInTheChannel extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DESCRIPTION_LENGTH = 900;
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Framework\App\Helper\Context $context)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context);
    }
    /**
     * {sc_ean} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string
     */
    public function scEan($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $int = isset($options['index']) && is_numeric($options['index']) && $options['index'] > 0 ? $options['index'] : 0;
        $value = explode(',', $item->getEan());
        if (isset($value[$int])) {
            $value = $value[$int];
        } else {
            $value = '';
        }
        return $value;
    }
    /**
     * {sc_images} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string
     */
    public function scImages($model, $options, $product, $reference)
    {
        $index = isset($options['index']) && is_numeric($options['index']) ? $options['index'] : 0;
        $item = $model->checkReference($reference, $product);
        $idCol = $this->moduleHelper->moduleIsEnabled('Magento_Enterprise') ? 'row_id' : 'entity_id';
        if ($item == null) {
            return '';
        }
        $baseImage = $item->getSmall_image();
        $smallImages = [$item->getImage(), $item->getThumbnail()];
        $cnt = 0;
        $images = [];
        if ($item->getSmall_image() && $item->getSmall_image() != 'no_selection') {
            $path = $item->getSmall_image();
            $value = $path;
            $images[] = $value;
            $cnt++;
        }
        $dd = 0;
        while (isset($model->gallery[$item->getData($idCol)]['src'][$dd]) && $cnt < 10) {
            if ($model->gallery[$item->getData($idCol)]['src'][$dd] != $baseImage) {
                if (in_array($model->gallery[$item->getData($idCol)]['src'][$dd], $smallImages) || $model->gallery[$item->getData($idCol)]['disabled'][$dd] != 1) {
                    $path = $model->gallery[$item->getData($idCol)]['src'][$dd];
                    $value = $path;
                    $images[] = $value;
                    $cnt++;
                }
            }
            $dd++;
        }
        if (isset($images[$index])) {
            return $images[$index];
        } else {
            return '';
        }
    }
    /**
     * {sc_description} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string
     */
    public function scDescription($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $content = $item->getDescription() . $item->getShortDescription();
        $iframe = "|<iframe(.*)</iframe>|U";
        preg_match($iframe, $content, $m);
        if ($m) {
            $content = $item->getAttributeText('manufacturer') . ' ' . $item->getName() . ' - Part number: ' . $item->getSku() . " - Category : {categories,[1],[1],[1]}";
        } else {
            if (isset($options['strip_tags'])) {
                $content = strip_tags(preg_replace(['!\\<br /\\>!isU', '!\\<br/\\>!isU', '!\\<br>!isU'], " ", $content));
            }
            if (isset($options['html_entity_decode'])) {
                $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            }
            if (isset($options['htmlentities'])) {
                $content = htmlspecialchars($content);
            }
            if (strlen($content) > self::DESCRIPTION_LENGTH) {
                $content = substr($content, 0, self::DESCRIPTION_LENGTH - 3);
                $s = strrpos($content, " ");
                $content = substr($content, 0, $s) . '...';
            }
        }
        if ($content == null) {
            $content = $item->getAttributeText('manufacturer') . ' ' . $item->getName() . ' - Part number: ' . $item->getSku() . " - Category : {categories,[1],[1],[1]}";
        }
        $content = str_replace('&#153;', '', preg_replace('/' . '[\\x00-\\x1F\\x7F]' . '|[\\x00-\\x7F][\\x80-\\xBF]+' . '|([\\xC0\\xC1]|[\\xF0-\\xFF])[\\x80-\\xBF]*' . '|[\\xC2-\\xDF]((?![\\x80-\\xBF])|[\\x80-\\xBF]{2,})' . '|[\\xE0-\\xEF](([\\x80-\\xBF](?![\\x80-\\xBF]))|' . '(?![\\x80-\\xBF]{2})|[\\x80-\\xBF]{3,})' . '/S', ' ', $content));
        return strip_tags($content);
    }
    /**
     * {sc_url} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string
     */
    public function scUrl($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $html = isset($options['html']) ? $options['html'] : '';
        $index = isset($options['index']) ? $options['index'] : '';
        if ($item->getRequest_path()) {
            $value = $model->storeUrl . $index . $item->getRequest_path() . $html;
        } else {
            $value = $item->getProductUrl();
        }
        return $value;
    }
    /**
     * {sc_condition} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string
     */
    public function scCondition($model, $options, $product, $reference)
    {
        unset($options);
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        return stristr($item->getName(), 'refurbished') ? 'refurbished' : 'new';
    }
}
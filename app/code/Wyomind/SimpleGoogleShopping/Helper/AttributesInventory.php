<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Helper;

use Magento\Framework\App\Helper\Context;
/**
 * Attributes management
 */
class AttributesInventory extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * AttributesInventory constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }
    /**
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @return int
     */
    public function getStockId($model)
    {
        $stockId = \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID;
        if ($model->inventoryStock) {
            $stockId = $model->stockId;
        }
        return $stockId;
    }
    /**
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param \Magento\Catalog\Model\Product $product
     * @param int $stockId
     * @return array
     */
    public function getStocks($model, $product, $stockId)
    {
        if (isset($model->inventoryStock[$stockId][$product->getId()])) {
            $stocks = $model->inventoryStock[$stockId][$product->getId()];
        } else {
            $stocks = ['is_salable' => 0, 'quantity' => 0];
        }
        return $stocks;
    }
    /**
     * {g_availability} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string the availability of the product enclosed between tags
     */
    public function availability($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $inStock = !isset($options['in_stock']) ? 'in stock' : $options['in_stock'];
        $outOfStock = !isset($options['out_of_stock']) ? 'out of stock' : $options['out_of_stock'];
        $availableForOrder = !isset($options['pre_order']) ? 'preorder' : $options['pre_order'];
        $stockId = $this->getStockId($model);
        $stockId = !isset($options['stock_id']) ? $stockId : $options['stock_id'];
        if ($item->getManageStock() && !$item->getUseConfigManageStock() && !$model->manageStock || $item->getUseConfigManageStock() && $model->manageStock || $item->getManageStock() && !$item->getUseConfigManageStock()) {
            if ($stockId == \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID) {
                if ($item->getIsInStock() > 0) {
                    if ($item->getTypeId() == 'configurable') {
                        if (isset($model->configurableQty[$item->getId()])) {
                            $qty = $model->configurableQty[$item->getId()];
                        } else {
                            $qty = $item->getQty();
                        }
                    } else {
                        $qty = $item->getQty();
                    }
                    if ($qty > 0) {
                        $value = $inStock;
                    } else {
                        if ($item->getBackorders() || $item->getUseConfigBackorders() && $model->backorders) {
                            $value = $availableForOrder;
                        } else {
                            $value = $outOfStock;
                        }
                    }
                } else {
                    $value = $outOfStock;
                }
            } else {
                $stocks = $this->getStocks($model, $item, $stockId);
                if ($stocks['is_salable'] > 0) {
                    if ($item->getTypeId() == 'configurable') {
                        $qty = $stocks['quantity'];
                    } else {
                        $qty = $stocks['quantity'];
                    }
                    if ($qty > 0) {
                        $value = $inStock;
                    } else {
                        if ($item->getBackorders() || $item->getUseConfigBackorders() && $model->backorders) {
                            $value = $availableForOrder;
                        } else {
                            $value = $outOfStock;
                        }
                    }
                } else {
                    $value = $outOfStock;
                }
            }
        } else {
            $value = $inStock;
        }
        return $value;
    }
    /**
     * {stock_status} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string quantity of the product
     */
    public function isInStock($model, $options, $product, $reference)
    {
        $inStock = !isset($options['in_stock']) ? 'in stock' : $options['in_stock'];
        $outOfStock = !isset($options['out_of_stock']) ? 'out of stock' : $options['out_of_stock'];
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $stockId = $this->getStockId($model);
        $stockId = !isset($options['stock_id']) ? $stockId : $options['stock_id'];
        if ($stockId == \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID) {
            $value = $item->getIs_in_stock() > 0 ? $inStock : $outOfStock;
        } else {
            $stocks = $this->getStocks($model, $item, $stockId);
            $value = $stocks['is_salable'] > 0 ? $inStock : $outOfStock;
        }
        return $value;
    }
    /**
     * {qty} attribute processing
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $model
     * @param array $options
     * @param \Magento\Catalog\Model\Product $product
     * @param string $reference
     * @return string quantity of the product
     */
    public function qty($model, $options, $product, $reference)
    {
        $item = $model->checkReference($reference, $product);
        if ($item == null) {
            return '';
        }
        $float = !isset($options['float']) ? 0 : $options['float'];
        $stockId = $this->getStockId($model);
        $stockId = !isset($options['stock_id']) ? $stockId : $options['stock_id'];
        if ($stockId == \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID) {
            if ($product->getTypeID() == 'configurable') {
                if (!isset($model->configurableQty[$product->getId()])) {
                    // configurable product without child
                    $value = number_format($item->getQty(), $float, '.', '');
                } else {
                    $value = number_format($model->configurableQty[$product->getId()], $float, '.', '');
                }
            } elseif ($reference == 'configurable') {
                $value = number_format($model->configurableQty[$item->getId()], $float, '.', '');
            } else {
                $value = number_format($item->getQty(), $float, '.', '');
            }
        } else {
            $stocks = $this->getStocks($model, $item, $stockId);
            if ($product->getTypeID() == 'configurable') {
                $value = number_format($stocks['quantity'], $float, '.', '');
            } else {
                $value = number_format($stocks['quantity'], $float, '.', '');
            }
        }
        return $value;
    }
}
<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */


















































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

/**
 * Class ProductPerformanceByManufacturer
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductPerformanceByManufacturer extends ProductPerformance
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_performance_manufacturer', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        if ($this->getManufacturerAttribute()) {
            $columns = $this->getColumns('children');
            $columns['manufacturer'] = 'manufacturer_value.value';

            /* @var $manufacturerAttr \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            $manufacturerAttr = $this->getManufacturerAttribute();
            $manufacturerTable = $manufacturerAttr->getBackendTable();

            $select =
                $this->joinChildrenItems()
                    ->columns($columns)
                    ->joinLeft(
                        ['product_entity' => $this->getTable('catalog_product_entity')],
                        'product_entity.entity_id = main_table.product_id',
                        []
                    )
                    ->joinLeft(
                        ['item_manufacturer' => $manufacturerTable],
                        'item_manufacturer.' . $this->getCatalogLinkField() . ' = product_entity.'
                        . $this->getCatalogLinkField() . ' AND item_manufacturer.attribute_id = '
                        . $manufacturerAttr->getId(),
                        []
                    )->joinLeft(
                        ['manufacturer_value' => $this->getTable('eav_attribute_option_value')],
                        'item_manufacturer.value = manufacturer_value.option_id AND manufacturer_value.store_id = 0',
                        []
                    )
                    ->where('manufacturer_value.value IS NOT NULL')
                    ->group($this->getGroupByFields(['manufacturer_value.value']));
            $select = $this->addFilterByCreatedAt($select, 'order');

            $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
        }
    }
}

<?php
namespace Meigee\CategoriesEnhanced\Plugin\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider {

    private $eavConfig;

    public function __construct(\Magento\Eav\Model\Config $eavConfig) {
        $this->eavConfig = $eavConfig;
    }

    public function afterPrepareMeta(\Magento\Catalog\Model\Category\DataProvider $subject, $result) {
        $meta = array_replace_recursive($result, $this->prepareFieldsMeta(
            $this->getFieldsMap(),
            $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'))
        ));
        return $meta;
    }

    private function prepareFieldsMeta($fieldsMap, $fieldsMeta) {
        $result = [];
        foreach ($fieldsMap as $fieldSet => $fields) {
            foreach ($fields as $field) {
                if (isset($fieldsMeta[$field])) {
                    $result[$fieldSet]['children'][$field]['arguments']['data']['config'] = $fieldsMeta[$field];
                }
            }
        }
        return $result;
    }

    protected function getFieldsMap() {
        $fields = parent::getFieldsMap();
        $fields['display_settings'][] = 'meigee_cat_menutype';
        $fields['display_settings'][] = 'meigee_cat_menu_width';
        $fields['display_settings'][] = 'meigee_cat_max_quantity';
        $fields['display_settings'][] = 'meigee_cat_ratio';
        $fields['display_settings'][] = 'meigee_cat_bg';
        $fields['display_settings'][] = 'meigee_cat_bg_option';
        $fields['display_settings'][] = 'meigee_cat_custom_link';
        $fields['display_settings'][] = 'meigee_cat_custom_link_target';
        $fields['display_settings'][] = 'meigee_cat_bold_link';
        $fields['display_settings'][] = 'meigee_cat_customlabel';
        $fields['display_settings'][] = 'meigee_cat_labeltext';
        $fields['display_settings'][] = 'meigee_menu_catimg';
        $fields['display_settings'][] = 'meigee_cat_block_right';
        $fields['display_settings'][] = 'meigee_cat_block_top';
        $fields['display_settings'][] = 'meigee_cat_block_bottom';
        $fields['display_settings'][] = 'meigee_cat_subcontent';
        return $fields;
    }
}

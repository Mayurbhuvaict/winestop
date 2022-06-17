<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoMarkup\Setup;

use Magento\Config\Model\Config\Factory as ConfigFactory;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * InstallSchema constructor.
     * @param ConfigFactory $configFactory
     */
    public function __construct(
        ConfigFactory $configFactory
    ) {
        $this->configFactory = $configFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer  = $setup;
        $connection = $installer->getConnection();

        $config = [
            'seo_snippets/product_snippets/rich_snippets_delete_wrong'                  => 'seo/seo_markup/product/is_remove_native_rs',
            'seo_snippets/product_snippets/rich_snippets_item_description'              => 'seo/seo_markup/product/description_type',
            'seo_snippets/product_snippets/rich_snippets_item_image'                    => 'seo/seo_markup/product/is_image_enabled',
            'seo_snippets/product_snippets/rich_snippets_item_availability'             => 'seo/seo_markup/product/is_availability_enabled',
            'seo_snippets/product_snippets/rich_snippets_payment_method'                => 'seo/seo_markup/product/is_accepted_payment_method_enabled',
            'seo_snippets/product_snippets/rich_snippets_delivery_method'               => 'seo/seo_markup/product/is_available_delivery_method_enabled',
            'seo_snippets/product_snippets/rich_snippets_product_category'              => 'seo/seo_markup/product/is_category_enabled',
            'seo_snippets/product_snippets/rich_snippets_manufacturer_part_number'      => 'seo/seo_markup/product/is_mpn_enabled',
            'seo_snippets/product_snippets/rich_snippets_brand_config'                  => 'seo/seo_markup/product/brand_attribute',
            'seo_snippets/product_snippets/rich_snippets_model_config'                  => 'seo/seo_markup/product/model_attribute',
            'seo_snippets/product_snippets/rich_snippets_color_config'                  => 'seo/seo_markup/product/color_attribute',
            'seo_snippets/product_snippets/rich_snippets_weight_config'                 => 'seo/seo_markup/product/weight_unit_type',
            'seo_snippets/product_snippets/rich_snippets_dimensions_config'             => 'seo/seo_markup/product/is_dimensions_enabled',
            'seo_snippets/product_snippets/rich_snippets_dimensional_unit'              => 'seo/seo_markup/product/dimension_unit',
            'seo_snippets/product_snippets/rich_snippets_height_config'                 => 'seo/seo_markup/product/dimension_height_attribute',
            'seo_snippets/product_snippets/rich_snippets_width_config'                  => 'seo/seo_markup/product/dimension_width_attribute',
            'seo_snippets/product_snippets/rich_snippets_depth_config'                  => 'seo/seo_markup/product/dimension_depth_attribute',
            'seo_snippets/product_snippets/rich_snippets_product_condition_config'      => 'seo/seo_markup/product/item_condition_type',
            'seo_snippets/product_snippets/rich_snippets_product_condition_attribute'   => 'seo/seo_markup/product/item_condition_attribute',
            'seo_snippets/product_snippets/rich_snippets_product_condition_new'         => 'seo/seo_markup/product/item_condition_attribute_value_new',
            'seo_snippets/product_snippets/rich_snippets_product_condition_used'        => 'seo/seo_markup/product/item_condition_attribute_value_used',
            'seo_snippets/product_snippets/rich_snippets_product_condition_refurbished' => 'seo/seo_markup/product/item_condition_attribute_value_refurbished',
            'seo_snippets/product_snippets/rich_snippets_product_condition_damaged'     => 'seo/seo_markup/product/item_condition_attribute_value_damaged',
            'seo_snippets/product_snippets/rich_snippets_individual_reviews_enabled'    => 'seo/seo_markup/product/is_individual_reviews_enabled',
            'seo_snippets/product_snippets/gtin8'                                       => 'seo/seo_markup/product/gtin8_attribute',
            'seo_snippets/product_snippets/gtin12'                                      => 'seo/seo_markup/product/gtin12_attribute',
            'seo_snippets/product_snippets/gtin13'                                      => 'seo/seo_markup/product/gtin13_attribute',
            'seo_snippets/product_snippets/gtin14'                                      => 'seo/seo_markup/product/gtin14_attribute',

            'seo_snippets/category_snippets/category_rich_snippets' => 'seo/seo_markup/category/is_rs_enabled',

            'seo_snippets/home_page_snippets/rich_snippets_delete_wrong_for_home_page' => 'seo/seo_markup/page/is_remove_native_rs',

            'seo_snippets/organization_snippets/is_organization_snippets'                      => 'seo/seo_markup/organization/is_rs_enabled',
            'seo_snippets/organization_snippets/name_organization_snippets'                    => 'seo/seo_markup/organization/is_custom_name',
            'seo_snippets/organization_snippets/manual_name_organization_snippets'             => 'seo/seo_markup/organization/custom_name',
            'seo_snippets/organization_snippets/country_address_organization_snippets'         => 'seo/seo_markup/organization/is_custom_address_country',
            'seo_snippets/organization_snippets/manual_country_address_organization_snippets'  => 'seo/seo_markup/organization/custom_address_country',
            'seo_snippets/organization_snippets/locality_address_organization_snippets'        => 'seo/seo_markup/organization/is_custom_address_locality',
            'seo_snippets/organization_snippets/manual_locality_address_organization_snippets' => 'seo/seo_markup/organization/custom_address_locality',
            'seo_snippets/organization_snippets/address_region_organization_snippets'          => 'seo/seo_markup/organization/is_custom_address_region',
            'seo_snippets/organization_snippets/manual_address_region_organization_snippets'   => 'seo/seo_markup/organization/custom_address_region',
            'seo_snippets/organization_snippets/postal_code_organization_snippets'             => 'seo/seo_markup/organization/is_custom_postal_code',
            'seo_snippets/organization_snippets/manual_postal_code_organization_snippets'      => 'seo/seo_markup/organization/custom_postal_code',
            'seo_snippets/organization_snippets/street_address_organization_snippets'          => 'seo/seo_markup/organization/is_custom_street_address',
            'seo_snippets/organization_snippets/manual_street_address_organization_snippets'   => 'seo/seo_markup/organization/custom_street_address',
            'seo_snippets/organization_snippets/telephone_organization_snippets'               => 'seo/seo_markup/organization/is_custom_telephone',
            'seo_snippets/organization_snippets/manual_telephone_organization_snippets'        => 'seo/seo_markup/organization/custom_telephone',
            'seo_snippets/organization_snippets/manual_faxnumber_organization_snippets'        => 'seo/seo_markup/organization/custom_fax_number',
            'seo_snippets/organization_snippets/email_organization_snippets'                   => 'seo/seo_markup/organization/is_custom_email',
            'seo_snippets/organization_snippets/manual_email_organization_snippets'            => 'seo/seo_markup/organization/custom_email',

            'seo_snippets/breadcrumbs_snippets/is_breadcrumbs' => 'seo/seo_markup/breadcrumb_list/is_rs_enabled',

            'seo_snippets/opengraph/is_category_opengraph' => 'seo/seo_markup/category/is_og_enabled',
            'seo_snippets/opengraph/is_cms_opengraph'      => 'seo/seo_markup/page/is_og_enabled',

            'seo_snippets/twitter/get_twitter_card' => 'seo/seo_markup/twitter/card_type',
            'seo_snippets/twitter/twitter_user'     => 'seo/seo_markup/twitter/username',
        ];

        foreach ($config as $oldPath => $newPath) {
            try {
                $connection->update(
                    $installer->getTable('core_config_data'),
                    ['path' => $newPath],
                    $connection->quoteInto('path = ?', $oldPath)
                );
            } catch (\Exception $e) {
            }
        }

        $installer->endSetup();
    }
}

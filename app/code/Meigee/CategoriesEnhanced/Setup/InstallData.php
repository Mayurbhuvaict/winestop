<?php
 
namespace Meigee\CategoriesEnhanced\Setup;
 
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
 
class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
 
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_max_quantity',
			[
				'type'                     => 'text',
				'label'                    => 'Max. Quantity of categories in 1 row:',
				'input'                    => 'text',
				'required'                 => false,
				'sort_order'               => 1,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => false,
				'is_html_allowed_on_front' => false,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_ratio',
			[
				'type'          => 'varchar',
				'label'         => 'Ratio',
				'input'         => 'select',
				'required'      => false,
				'source'        => 'Meigee\CategoriesEnhanced\Model\Category\Attribute\Source\Ratio',
				'sort_order'    => 2,
				'global'        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'         => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_bg',
			[
				'type'       => 'varchar',
				'label'      => 'Upload your top level category background image',
				'input'      => 'image',
				'required'   => false,
				'backend'    => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
				'sort_order' => 3,
				'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'      => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_bg_option',
			[
				'type'       => 'varchar',
				'label'      => 'Ratio',
				'input'      => 'select',
				'required'   => true,
				'source'     => 'Meigee\CategoriesEnhanced\Model\Category\Attribute\Source\Bgpos',
				'sort_order' => 4,
				'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'      => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_block_right',
			[
				'type'                     => 'text',
				'label'                    => 'Right Content',
				'input'                    => 'textarea',
				'required'                 => false,
				'sort_order'               => 8,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => true,
				'is_html_allowed_on_front' => true,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_block_bottom',
			[
				'type'                     => 'text',
				'label'                    => 'Top Content',
				'input'                    => 'textarea',
				'required'                 => false,
				'sort_order'               => 9,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => true,
				'is_html_allowed_on_front' => true,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_block_top',
			[
				'type'                     => 'text',
				'label'                    => 'Bottom Content',
				'input'                    => 'textarea',
				'required'                 => false,
				'sort_order'               => 10,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => true,
				'is_html_allowed_on_front' => true,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);

		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_custom_link',
			[
				'type'                     => 'text',
				'label'                    => 'Custom link',
				'input'                    => 'text',
				'required'                 => false,
				'sort_order'               => 5,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => false,
				'is_html_allowed_on_front' => false,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_custom_link_target',
			[
				'type'       => 'int',
				'label'      => 'Link target blank',
				'input'      => 'select',
				'required'   => false,
				'source'     => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
				'sort_order' => 6,
				'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'      => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_customlabel',
			[
				'type'       => 'varchar',
				'label'      => 'Category Label',
				'input'      => 'select',
				'required'   => false,
				'source'     => 'Meigee\CategoriesEnhanced\Model\Category\Attribute\Source\Customlabel',
				'sort_order' => 7,
				'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'      => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_labeltext',
			[
				'type'                     => 'text',
				'label'                    => 'Label Text',
				'input'                    => 'text',
				'required'                 => false,
				'sort_order'               => 8,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => false,
				'is_html_allowed_on_front' => false,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);	
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_menutype',
			[
				'type'       => 'varchar',
				'label'      => 'Top Level Dropdown Menu Type',
				'input'      => 'select',
				'required'   => false,
				'source'     => 'Meigee\CategoriesEnhanced\Model\Category\Attribute\Source\Menutype',
				'sort_order' => 9,
				'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'      => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_menu_width',
			[
				'type'                     => 'text',
				'label'                    => 'Top Level Dropdown Menu Width',
				'input'                    => 'text',
				'required'                 => false,
				'sort_order'               => 10,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => false,
				'is_html_allowed_on_front' => false,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_subcontent',
			[
				'type'                     => 'text',
				'label'                    => 'Custom Drop Down Content',
				'input'                    => 'textarea',
				'required'                 => false,
				'sort_order'               => 11,
				'global'                   => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'wysiwyg_enabled'          => true,
				'is_html_allowed_on_front' => true,
				'group'                    => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_cat_bold_link',
			[
				'type'       => 'int',
				'label'      => 'Bold link design',
				'input'      => 'select',
				'required'   => false,
				'source'     => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
				'sort_order' => 12,
				'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'      => 'Meigee/CategoriesEnhanced',
			]
		);
		$eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'meigee_menu_catimg',
			[
				'type'       => 'varchar',
				'label'      => 'Menu Category Image',
				'input'      => 'image',
				'required'   => false,
				'backend'    => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
				'sort_order' => 13,
				'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
				'group'      => 'Meigee/CategoriesEnhanced',
			]
		);
 
        $setup->endSetup();
    }
}
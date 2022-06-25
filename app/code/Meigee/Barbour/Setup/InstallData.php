<?php


namespace Meigee\Barbour\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
	
	protected $resourceConnection;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection, EavSetupFactory $eavSetupFactory)
    {
		$this->resourceConnection = $resourceConnection;
		$this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'hover_image',
            [
                'type' => 'varchar',
                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Frontend\Image',
                'label' => 'Hover Image',
                'input' => 'media_image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'required' => false,
                'used_in_product_listing' => true,
                'sort_order' => 10
                //'group' => 'Images' /* This line is deactivated because it can cause an error like "1062 Duplicate entry" 4-Images'...". Instead, the code below is used */
            ]
        );
		
		/* Get DB Connection */
		$this->resourceConnection = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
		$connection = $this->resourceConnection->getConnection();
		$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);    
		$obj = $bootstrap->getObjectManager();
		$deploymentConfig = $obj->get('Magento\Framework\App\DeploymentConfig');
		$tablePrefix = $deploymentConfig->get('db/table_prefix');
		
		/* Move "hover_image" attribute into "Images" groupe for each attribute set */
		$entityTypeId = $eavSetup->getEntityTypeId('catalog_product');
		$attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
		foreach($attributeSetIds as $attributeSetId){
			$attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Images');
			$attributeId = $eavSetup->getAttributeId($entityTypeId, 'hover_image');
			$sql = "UPDATE ".$tablePrefix."eav_entity_attribute SET attribute_group_id=".$attributeGroupId." WHERE attribute_id=".$attributeId." AND attribute_set_id=".$attributeSetId;
			$connection->query($sql);
		}
    }
}
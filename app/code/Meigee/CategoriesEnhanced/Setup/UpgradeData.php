<?php
 
namespace Meigee\CategoriesEnhanced\Setup;
 
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
 
class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;
 
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.2') < 0) {
		
			
				
			}
            
        
 
        $setup->endSetup();
    }
}
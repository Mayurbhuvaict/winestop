<?php
namespace Aheadworks\OneStepCheckout\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class ConfigSetup
 * @package Aheadworks\OneStepCheckout\Setup
 */
class ConfigSetup
{
    /**
     * Restore to default config values
     *
     * @param ModuleDataSetupInterface $setup
     * @param string $path
     * @return $this
     */
    public function restoreToDefault(ModuleDataSetupInterface $setup, $path)
    {
        $connection = $setup->getConnection();
        $connection->delete($setup->getTable('core_config_data'), ['path = ?' => $path]);
        return $this;
    }
}

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



namespace Mirasvit\SeoContent\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var UpgradeDataInterface[]
     */
    private $pool;

    /**
     * UpgradeData constructor.
     * @param UpgradeData\UpgradeData101 $upgrade101
     * @param UpgradeData\UpgradeData102 $upgrade102
     */
    public function __construct(
        UpgradeData\UpgradeData101 $upgrade101,
        UpgradeData\UpgradeData102 $upgrade102
    ) {
        $this->pool = [
            '1.0.1' => $upgrade101,
            '1.0.2' => $upgrade102,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        foreach ($this->pool as $version => $update) {
            if (version_compare($context->getVersion(), $version) < 0) {
                $update->upgrade($setup, $context);
            }
        }

        $installer->endSetup();
    }
}

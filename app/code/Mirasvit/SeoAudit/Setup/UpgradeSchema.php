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


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Setup;


use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var UpgradeSchemaInterface[]
     */
    private $pool;
    
    public function __construct(
        UpgradeSchema\UpgradeSchema101 $upgradeSchema101
    ) {
        $this->pool = [
            '1.0.1' => $upgradeSchema101
        ];
    }
    
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        foreach ($this->pool as $version => $upgrade) {
            if (version_compare($context->getVersion(), $version, "<")) {
                $upgrade->upgrade($setup, $context);
            }
        }

        $setup->endSetup();
    }
}
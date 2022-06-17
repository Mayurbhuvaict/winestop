<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Wyomind\CronScheduler\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Wyomind\CronScheduler\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Wyomind\Framework\Helper\ModuleFactory
     */
    public $license;

    /**
     * UpgradeSchema constructor.
     * @param \Wyomind\Framework\Helper\License\UpdateFactory $license
     */
    public function __construct(\Wyomind\Framework\Helper\License\UpdateFactory $license)
    {
        $this->license = $license;
    }


    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
    
        $this->license->create()->update(__CLASS__, $context);

    }
}

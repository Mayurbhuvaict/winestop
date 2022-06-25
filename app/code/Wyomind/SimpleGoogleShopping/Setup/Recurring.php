<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Setup;

class Recurring implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @var \Wyomind\Framework\Helper\Install|null
     */
    private $installHelper = null;

    /**
     * @param \Wyomind\Framework\Helper\Install $installHelper
     */
    public function __construct(
        \Wyomind\Framework\Helper\Install $installHelper
    ) {
    
        $this->installHelper = $installHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
    
        $files = ['Model/ResourceModel/Product/Collection.php'];
        $this->installHelper->copyFilesByMagentoVersion(__FILE__, $files);
    }
}

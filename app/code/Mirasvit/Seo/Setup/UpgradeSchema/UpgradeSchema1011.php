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



namespace Mirasvit\Seo\Setup\UpgradeSchema;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Module\Status as ModuleStatus;

class UpgradeSchema1011 implements UpgradeSchemaInterface
{
    /**
     * @var ModuleStatus
     */
    private $moduleStatus;

    /**
     * UpgradeSchema1011 constructor.
     * @param ModuleStatus $moduleStatus
     */
    public function __construct(
        ModuleStatus $moduleStatus
    ) {
        $this->moduleStatus = $moduleStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->moduleStatus->setIsEnabled(true, [
            'Mirasvit_SeoContent',
            'Mirasvit_SeoToolbar',
        ]);
    }
}

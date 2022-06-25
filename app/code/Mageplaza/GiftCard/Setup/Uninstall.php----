<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Class Uninstall
 * @package Mageplaza\SizeChart\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Uninstall constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(Product::ENTITY, 'gift_code_pattern');
        $eavSetup->removeAttribute(Product::ENTITY, 'gift_card_type');
        $eavSetup->removeAttribute(Product::ENTITY, 'gift_card_amounts');
        $eavSetup->removeAttribute(Product::ENTITY, 'allow_amount_range');
        $eavSetup->removeAttribute(Product::ENTITY, 'min_amount');
        $eavSetup->removeAttribute(Product::ENTITY, 'max_amount');
        $eavSetup->removeAttribute(Product::ENTITY, 'price_rate');
        $eavSetup->removeAttribute(Product::ENTITY, 'can_redeem');
        $eavSetup->removeAttribute(Product::ENTITY, 'expire_after_day');
        $eavSetup->removeAttribute(Product::ENTITY, 'gift_product_template');
    }
}

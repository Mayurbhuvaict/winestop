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

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\Boolean;
use Magento\Catalog\Model\Product\Attribute\Backend\Price;
use Magento\Catalog\Model\Product\AttributeSet\Options;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;
use Mageplaza\GiftCard\Model\Attribute\Backend\Amount;
use Mageplaza\GiftCard\Model\Attribute\Backend\MultiSelect;
use Mageplaza\GiftCard\Model\Attribute\Backend\Pattern;
use Mageplaza\GiftCard\Model\GiftCard\Template;
use Mageplaza\GiftCard\Model\Product\DeliveryMethods;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class InstallData
 * @package Mageplaza\GiftCard\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var Options
     */
    protected $_attributeSet;

    /**
     * Install Data constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     * @param BlockFactory $blockFactory
     * @param TemplateFactory $templateFactory
     * @param Options $attributeSet
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        CategorySetupFactory $categorySetupFactory,
        BlockFactory $blockFactory,
        TemplateFactory $templateFactory,
        Options $attributeSet
    ) {
        $this->eavSetupFactory      = $eavSetupFactory;
        $this->salesSetupFactory    = $salesSetupFactory;
        $this->quoteSetupFactory    = $quoteSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->blockFactory         = $blockFactory;
        $this->templateFactory      = $templateFactory;
        $this->_attributeSet        = $attributeSet;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /** @var SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        /** @var QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $installer->startSetup();

        /** Add field to process gift card discount amount */
        $salesInstaller->addAttribute('order', 'gift_cards', ['type' => 'text']);
        $salesInstaller->addAttribute('order', 'base_gift_card_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'gift_card_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'base_gift_credit_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'gift_credit_amount', ['type' => 'decimal']);

        $salesInstaller->addAttribute('invoice', 'base_gift_card_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('invoice', 'gift_card_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('invoice', 'base_gift_credit_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('invoice', 'gift_credit_amount', ['type' => 'decimal']);

        $salesInstaller->addAttribute('creditmemo', 'base_gift_card_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('creditmemo', 'gift_card_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('creditmemo', 'base_gift_credit_amount', ['type' => 'decimal']);
        $salesInstaller->addAttribute('creditmemo', 'gift_credit_amount', ['type' => 'decimal']);

        $quoteInstaller->addAttribute('quote', 'gift_cards', ['type' => 'text']);
        $quoteInstaller->addAttribute('quote', 'gc_credit', ['type' => 'text']);

        /** Create product attribute group */
        $entityTypeId = $catalogSetup->getEntityTypeId(Category::ENTITY);

        foreach ($this->_attributeSet->toOptionArray() as $set) {
            $catalogSetup->addAttributeGroup($entityTypeId, $set['value'], 'Gift Card Information', 10);
        }

        /** Add Product Attribute */
        $catalogSetup->addAttribute(Product::ENTITY, 'gift_code_pattern', array_merge($this->getDefaultOptions(), [
            'label'          => 'Gift Code Pattern',
            'type'           => 'varchar',
            'input'          => 'text',
            'backend'        => Pattern::class,
            'input_renderer' => \Mageplaza\GiftCard\Block\Adminhtml\Product\Helper\Form\Config\Pattern::class,
            'required'       => true,
            'sort_order'     => 10
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'gift_card_type', array_merge($this->getDefaultOptions(), [
            'label'      => 'Gift Card Type',
            'type'       => 'int',
            'input'      => 'select',
            'source'     => DeliveryMethods::class,
            'global'     => ScopedAttributeInterface::SCOPE_GLOBAL,
            'sort_order' => 20
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'gift_card_amounts', array_merge($this->getDefaultOptions(), [
            'label'      => 'Gift Card Amounts',
            'type'       => 'varchar',
            'input'      => 'text',
            'backend'    => Amount::class,
            'global'     => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 30
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'allow_amount_range', array_merge($this->getDefaultOptions(), [
            'label'      => 'Allow Amount Range',
            'type'       => 'int',
            'input'      => 'select',
            'source'     => Product\Attribute\Source\Boolean::class,
            'global'     => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 40
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'min_amount', array_merge($this->getDefaultOptions(), [
            'label'      => 'Min Amount',
            'type'       => 'decimal',
            'input'      => 'price',
            'backend'    => Price::class,
            'class'      => 'validate-number',
            'global'     => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 50
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'max_amount', array_merge($this->getDefaultOptions(), [
            'label'      => 'Max Amount',
            'type'       => 'decimal',
            'input'      => 'price',
            'backend'    => Price::class,
            'class'      => 'validate-number',
            'global'     => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 60
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'price_rate', array_merge($this->getDefaultOptions(), [
            'label'      => 'Price Percentage',
            'type'       => 'decimal',
            'input'      => 'text',
            'class'      => 'validate-number',
            'global'     => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 65
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'can_redeem', array_merge($this->getDefaultOptions(), [
            'label'      => 'Can Redeem',
            'type'       => 'int',
            'input'      => 'select',
            'source'     => Product\Attribute\Source\Boolean::class,
            'backend'    => Boolean::class,
            'global'     => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 80
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'expire_after_day', array_merge($this->getDefaultOptions(), [
            'label'      => 'Expire After (days)',
            'type'       => 'varchar',
            'input'      => 'text',
            'backend'    => Pattern::class,
            'global'     => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order' => 90
        ]));
        $catalogSetup->addAttribute(Product::ENTITY, 'gift_product_template', array_merge($this->getDefaultOptions(), [
            'label'      => 'Template',
            'type'       => 'text',
            'input'      => 'multiselect',
            'source'     => Template::class,
            'backend'    => MultiSelect::class,
            'sort_order' => 100
        ]));

        $this->addRemoveApply($catalogSetup);

        /** Add Gift Product head block */
        $block = $this->blockFactory->create()
            ->load('gift_card_products', 'identifier');
        if (!$block->getId()) {
            $headBlock = [
                'title'      => 'Gift Card Product',
                'identifier' => 'gift_card_products',
                'stores'     => [0],
                'is_active'  => 1,
                'content'    => '<p><img src="{{media url="mageplaza/default/gift_product_banner.jpg"}}" alt="Gift Product Banner" width="100%" /></p>'
            ];
            $block->setData($headBlock)->save();
        }

        /** Add sample template data */
        $sampleTemplates = [
            [
                'name'   => 'Amazon Style',
                'status' => 1,
                'title'  => 'Gift Card',
                'design' => '{"giftcard":{"label":"Gift Card","width":500,"height":530,"css":{"border-radius":"5px"}},"image":{"label":"Image","sampleContent":"Image","width":500,"height":300,"top":0,"left":0},"message":{"label":"Message","sampleContent":"Hope you enjoy this gift card!","width":500,"height":100,"top":300,"left":0,"css":{"border-bottom":"1px solid #ccc","font-size":"15px","color":"#000","padding":"25px"}},"value":{"label":"Value","sampleContent":"$100.00","width":176,"height":50,"top":410,"left":30,"css":{"font-size":"40px","padding":"5px","color":"rgb(68, 68, 68)"}},"title":{"label":"Title","sampleContent":"Gift Card","width":104,"height":40,"top":480,"left":40,"css":{"font-size":"18px","color":"#868686"}},"logo":{"label":"Logo","sampleContent":"Logo","width":150,"height":100,"top":410,"left":320}}'
            ]
        ];
        $setup->getConnection()->insertMultiple($installer->getTable('mageplaza_giftcard_template'), $sampleTemplates);

        $installer->endSetup();
    }

    /**
     * Add/remove field from gift card product
     *
     * @param CategorySetup $catalogSetup
     *
     * @return $this
     */
    protected function addRemoveApply($catalogSetup)
    {
        $fieldAdd = ['weight'];
        foreach ($fieldAdd as $field) {
            $applyTo = $catalogSetup->getAttribute('catalog_product', $field, 'apply_to');
            if ($applyTo) {
                $applyTo = explode(',', $applyTo);
                if (!in_array(GiftCard::TYPE_GIFTCARD, $applyTo, true)) {
                    $applyTo[] = GiftCard::TYPE_GIFTCARD;
                    $catalogSetup->updateAttribute('catalog_product', 'weight', 'apply_to', implode(',', $applyTo));
                }
            }
        }

        $fieldRemove = ['cost'];
        foreach ($fieldRemove as $field) {
            $applyTo = explode(',', $catalogSetup->getAttribute('catalog_product', $field, 'apply_to'));
            if (in_array(GiftCard::TYPE_GIFTCARD, $applyTo, true)) {
                foreach ($applyTo as $k => $v) {
                    if ($v === GiftCard::TYPE_GIFTCARD) {
                        unset($applyTo[$k]);
                        break;
                    }
                }
                $catalogSetup->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'group'                   => 'Gift Card Information',
            'backend'                 => '',
            'frontend'                => '',
            'class'                   => '',
            'source'                  => '',
            'global'                  => ScopedAttributeInterface::SCOPE_STORE,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => true,
            'default'                 => '',
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'unique'                  => false,
            'apply_to'                => GiftCard::TYPE_GIFTCARD,
            'used_in_product_listing' => true
        ];
    }
}

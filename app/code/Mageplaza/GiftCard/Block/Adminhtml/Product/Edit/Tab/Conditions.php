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

namespace Mageplaza\GiftCard\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Catalog\Model\Product;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\RuleFactory;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;
use Mageplaza\GiftCard\Ui\DataProvider\Product\Modifier\GiftCard as GiftCardField;

/**
 * Class Conditions
 * @package Mageplaza\GiftCard\Block\Adminhtml\Product\Edit\Tab
 */
class Conditions extends \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions
{
    /**
     * @var string
     */
    protected $_nameInLayout = 'mpgiftcard_conditions';

    /**
     * @var RuleFactory|null
     */
    private $ruleFactory;

    /**
     * Conditions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param Fieldset $rendererFieldset
     * @param array $data
     * @param RuleFactory|null $ruleFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        Fieldset $rendererFieldset,
        RuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->ruleFactory = $ruleFactory;

        parent::__construct($context, $registry, $formFactory, $conditions, $rendererFieldset, $data, $ruleFactory);
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->getProduct();

        if ($model->getTypeId() !== GiftCard::TYPE_GIFTCARD) {
            return $this;
        }

        $rule = $this->ruleFactory->create();

        $rule->setConditionsSerialized($model->getData(GiftCardField::FIELD_CONDITIONS));

        $form = $this->addTabToForm($rule, 'mpgiftcard_conditions_fieldset', 'product_form');

        $this->setForm($form);

        return $this;
    }

    /**
     * @return string
     */
    public function getFormHtml()
    {
        $model = $this->getProduct();

        if ($model->getTypeId() !== GiftCard::TYPE_GIFTCARD) {
            return '<style type="text/css">.fieldset-wrapper.mpgiftcard_conditions {display: none;}</style>';
        }

        return parent::getFormHtml();
    }

    /**
     * @return Product
     */
    protected function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
}

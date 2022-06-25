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

namespace Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions;
use Magento\SalesRule\Model\RuleFactory;
use Mageplaza\GiftCard\Model\Pool;

/**
 * Class Condition
 * @package Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab
 */
class Condition extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    private $rendererFieldset;

    /**
     * @var Conditions
     */
    private $conditions;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * Condition constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Conditions $conditions
     * @param Fieldset $rendererFieldset
     * @param RuleFactory $ruleFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Conditions $conditions,
        Fieldset $rendererFieldset,
        RuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions       = $conditions;
        $this->ruleFactory      = $ruleFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $rule = $this->ruleFactory->create();

        $rule->setConditionsSerialized($this->getObject()->getConditionsSerialized());

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');
        $form->setFieldNameSuffix('rule');

        $renderer = $this->rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl(
                $this->getUrl('mpgiftcard/condition/newConditionHtml/form/rule_conditions_fieldset')
            );

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            ['legend' => __('Apply the rule only if the following conditions are met (leave blank for all products).')]
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', [
            'name'  => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions')
        ])->setRule($rule)->setRenderer($this->conditions);

        $rule->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $this->setConditionFormName($rule->getConditions(), 'rule_conditions_fieldset');

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return Pool
     */
    protected function getObject()
    {
        return $this->_coreRegistry->registry('current_pool');
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Gift Code Condition');
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('Gift Code Condition');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}

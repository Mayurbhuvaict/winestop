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



namespace Mirasvit\SeoContent\Ui\Template\Form\Block;

use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as FieldsetRenderer;

class Rule extends Form implements TabInterface
{
    /**
     * @var FieldsetRenderer
     */
    protected $fieldsetRenderer;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $urlManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_serialized';

    /**
     * Rule constructor.
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param FieldsetRenderer $fieldsetRenderer
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        \Magento\Rule\Block\Conditions $conditions,
        FieldsetRenderer $fieldsetRenderer,
        FormFactory $formFactory,
        Registry $registry,
        Context $context
    ) {
        $this->fieldsetRenderer = $fieldsetRenderer;
        $this->conditions = $conditions;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $formName = \Mirasvit\SeoContent\Model\Template\Rule::FORM_NAME;

        /** @var TemplateInterface $template */
        $template = $this->registry->registry(TemplateInterface::class);
        $rule = $template->getRule();

        $form = $this->formFactory->create();
        $form->setData('html_id_prefix', 'rule_');

        $fieldsetName = 'conditions_fieldset';

        $renderer = $this->fieldsetRenderer
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setData('new_child_url', $this->getUrl('*/template/newConditionHtml', [
                'form'                       => 'rule_' . $fieldsetName,
                'form_name'                  => $formName,
                TemplateInterface::RULE_TYPE => $template->getRuleType(),
            ]));

        $fieldset = $form->addFieldset($fieldsetName, [
            'legend' => __('Conditions (leave blank for all elements, depending from rule type)'),
        ])->setRenderer($renderer);

        $rule->getConditions()
            ->setRuleType($template->getRuleType())
            ->setFormName($formName);

        $conditionsField = $fieldset->addField('conditions', 'text', [
            'name'           => 'conditions',
            'required'       => true,
            'data-form-part' => $formName,
        ]);

        $conditionsField->setRule($rule)
            ->setRenderer($this->conditions)
            ->setFormName($formName);

        $form->setValues($template->getData());
        $this->setConditionFormName($rule->getConditions(), $formName);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param object $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName($conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}

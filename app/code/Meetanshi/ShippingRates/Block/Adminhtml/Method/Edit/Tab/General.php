<?php

namespace Meetanshi\ShippingRates\Block\Adminhtml\Method\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;
use Meetanshi\ShippingRates\Helper\Data;

class General extends Form
{
    protected $systemStore;
    protected $formFactory;
    protected $registry;
    protected $context;
    protected $helper;

    public function __construct(SystemStore $systemStore, FormFactory $formFactory, Registry $registry, Context $context, Data $helper)
    {
        $this->systemStore = $systemStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->helper = $helper;

        parent::__construct($context);
    }

    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        $helper = $this->helper;

        $fieldInfo = $form->addFieldset('general', ['legend' => __('General')]);
        $fieldInfo->addField('name', 'text', [
            'label' => __('Name'),
            'required' => true,
            'name' => 'name',
            'note' => 'Variable {day} will be replaced with the estimated delivery value from the corresponding CSV column',
        ]);

        $fieldInfo->addField('is_active', 'select', [
            'label' => __('Status'),
            'name' => 'is_active',
            'options' => $helper->getStatuses(),
        ]);

        $fieldInfo->addField('pos', 'text', [
            'label' => __('Priority'),
            'name' => 'pos',
        ]);

        $fieldInfo->addField('select_rate', 'select', [
            'label' => __('For products with different shipping types'),
            'name' => 'select_rate',
            'values' => [
                [
                    'value' => '0',
                    'label' => __('Sum up rates')
                ],
                [
                    'value' => '1',
                    'label' => __('Select maximal rate')
                ],
                [
                    'value' => '2',
                    'label' => __('Select minimal rate')
                ]]
        ]);

        $fieldStore = $form->addFieldset('apply_in', ['legend' => __('Visible In')]);
        $fieldStore->addField('stores', 'multiselect', [
            'label' => __('Stores'),
            'name' => 'stores[]',
            'values' => $this->systemStore->getStoreValuesForForm(),
            'note' => __('Leave empty if there are no restrictions'),
        ]);

        $fieldCustomer = $form->addFieldset('apply_for', ['legend' => __('Applicable For')]);
        $fieldCustomer->addField('cust_groups', 'multiselect', [
            'name' => 'cust_groups[]',
            'label' => __('Customer Groups'),
            'values' => $helper->getAllGroups(),
            'note' => __('Leave empty if there are no restrictions'),
        ]);

        $form->setValues($this->registry->registry('shippingrates_method')->getData());

        return parent::_prepareForm();
    }
}

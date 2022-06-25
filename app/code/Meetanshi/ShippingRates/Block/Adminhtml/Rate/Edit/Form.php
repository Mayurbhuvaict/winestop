<?php

namespace Meetanshi\ShippingRates\Block\Adminhtml\Rate\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Meetanshi\ShippingRates\Helper\Data;

class Form extends WidgetForm
{
    protected $formFactory;
    protected $registry;
    protected $helper;
    protected $backendSession;

    public function __construct(FormFactory $formFactory, Context $context, Registry $registry, Data $helper)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->helper = $helper;
        parent::__construct($context);
    }

    protected function _prepareForm()
    {
        $form = $this->formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]), 'method' => 'post']]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $helper = $this->helper;
        $model = $this->registry->registry('shippingrates_rate');

        $fieldDestination = $form->addFieldset('destination', ['legend' => __('Destination')]);

        $fieldDestination->addField('method_id', 'hidden', [
            'name' => 'method_id',
        ]);

        $fieldDestination->addField('country', 'select', [
            'label' => __('Country'),
            'name' => 'country',
            'options' => $helper->getCountries(),
        ]);

        $fieldDestination->addField('state', 'select', [
            'label' => __('State'),
            'name' => 'state',
            'options' => $helper->getStates(),
        ]);

        $fieldDestination->addField('city', 'text', [
            'label' => __('City'),
            'name' => 'city',
        ]);

        $fieldDestination->addField('zip_from', 'text', [
            'label' => __('Zip From'),
            'name' => 'zip_from',
        ]);

        $fieldDestination->addField('zip_to', 'text', [
            'label' => __('Zip To'),
            'name' => 'zip_to',
        ]);

        $fieldTotals = $form->addFieldset('conditions', ['legend' => __('Conditions')]);
        $fieldTotals->addField('weight_from', 'text', [
            'label' => __('Weight From'),
            'name' => 'weight_from',
        ]);
        $fieldTotals->addField('weight_to', 'text', [
            'label' => __('Weight To'),
            'name' => 'weight_to',
        ]);

        $fieldTotals->addField('qty_from', 'text', [
            'label' => __('Qty From'),
            'name' => 'qty_from',
        ]);
        $fieldTotals->addField('qty_to', 'text', [
            'label' => __('Qty To'),
            'name' => 'qty_to',
        ]);

        $fieldTotals->addField('shipping_type', 'select', [
            'label' => __('Shipping Type'),
            'name' => 'shipping_type',
            'options' => $helper->getShippingType(),
        ]);

        $fieldTotals->addField('price_from', 'text', [
            'label' => __('Price From'),
            'name' => 'price_from',
            'note' => __('Original product cart price, without discounts.'),
        ]);

        $fieldTotals->addField('price_to', 'text', [
            'label' => __('Price To'),
            'name' => 'price_to',
            'note' => __('Original product cart price, without discounts.'),
        ]);

        $fieldRate = $form->addFieldset('rate', ['legend' => __('Rate')]);

        $fieldRate->addField('cost_base', 'text', [
            'label' => __('Base Rate for the Order'),
            'name' => 'cost_base',
        ]);

        $fieldRate->addField('cost_percent', 'text', [
            'label' => __('Percentage per Product'),
            'name' => 'cost_percent',
        ]);

        $fieldRate->addField('cost_product', 'text', [
            'label' => __('Fixed Rate per Product'),
            'name' => 'cost_product',
        ]);

        $fieldRate->addField('cost_weight', 'text', [
            'label' => __('Fixed Rate per 1 unit of weight'),
            'name' => 'cost_weight',
        ]);

        $fieldTotals->addField('time_delivery', 'text', [
            'label' => __('Estimated Delivery (days)'),
            'name' => 'time_delivery',
        ]);

        $data = $this->_backendSession->getFormData(true);
        if ($data) {
            $form->setValues($data);
            $this->_backendSession->setFormData(null);
        } elseif ($model) {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
    }
}

<?php

namespace Magecomp\Extrafee\Block\Adminhtml\Category\Edit\Tab;

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
        $fieldInfo->addField('category_name', 'text', [
            'label' => __('Name'),
            'required' => true,
            'name' => 'category_name',
            'note' => 'Category Name',
        ]);

        $fieldInfo->addField('is_active', 'select', [
            'label' => __('Status'),
            'name' => 'is_active',
            'options' => $helper->getStatuses(),
            /*'options' => [
                '0' => __('Inactive'),
                '1' => __('Active'),
            ],*/
        ]);

        $fieldInfo->addField('position', 'text', [
            'label' => __('Position'),
            'name' => 'position',
        ]);

        

        $form->setValues($this->registry->registry('shippingrates_category')->getData());

        return parent::_prepareForm();
    }
}

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

namespace Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab;

use IntlDateFormatter;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Locale\Timezone;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Directory\Helper\Data;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\Product\DeliveryMethods;
use Mageplaza\GiftCard\Model\ResourceModel\Template\CollectionFactory;

/**
 * Class Send
 * @package Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab
 */
class Send extends Generic implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    protected $templateFactory;

    /**
     * @var DeliveryMethods
     */
    protected $deliveryMethods;

    /**
     * @var FieldFactory
     */
    protected $_fieldFactory;

    /**
     * @var Template
     */
    protected $_templateHelper;

    /**
     * @var Timezone
     */
    protected $timezoneSource;

    /**
     * Send constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CollectionFactory $templateFactory
     * @param DeliveryMethods $deliveryMethods
     * @param FieldFactory $fieldFactory
     * @param Template $helperTemplate
     * @param Timezone $timezoneSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $templateFactory,
        DeliveryMethods $deliveryMethods,
        FieldFactory $fieldFactory,
        Template $helperTemplate,
        Timezone $timezoneSource,
        array $data = []
    ) {
        $this->templateFactory = $templateFactory;
        $this->deliveryMethods = $deliveryMethods;
        $this->_fieldFactory   = $fieldFactory;
        $this->_templateHelper = $helperTemplate;
        $this->timezoneSource  = $timezoneSource;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /* @var $model GiftCard */
        $model = $this->_coreRegistry->registry('current_giftcard');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        /** @var Dependence $dependencies */
        $dependencies = $this->getLayout()->createBlock(Dependence::class);

        /** Template Fieldset */
        $templateFieldset = $form->addFieldset('template_fieldset', ['legend' => __('Sending Information')]);

        /** Add template fields: image, sender name, recipient name, message */
        $this->_templateHelper->getTemplateFieldSet($model, $templateFieldset, $dependencies);

        /** Delivery Fieldset */
        $deliveryFieldset = $form->addFieldset('delivery_fieldset', ['legend' => __('Delivery')]);
        $deliveryFieldset->addField('delivery_method', 'select', [
            'name'   => 'delivery_method',
            'label'  => __('Delivery Method'),
            'title'  => __('Delivery Method'),
            'values' => DeliveryMethods::getMethodOptionArrayForForm()
        ]);
        $deliveryFieldset->addField('recipient_email', 'text', [
            'name'     => 'recipient_email',
            'label'    => __('Recipient Email'),
            'title'    => __('Recipient Email'),
            'class'    => 'validate-email',
            'required' => true
        ]);
        $deliveryFieldset->addField('recipient_phone', 'text', [
            'name'     => 'recipient_phone',
            'label'    => __('Phone Number'),
            'title'    => __('Phone Number'),
            'required' => true
        ]);
        $deliveryFieldset->addField('customer_email', 'text', [
            'name'     => 'customer_email',
            'label'    => __('Customer Email'),
            'title'    => __('Customer Email'),
            'class'    => 'validate-email',
            'required' => true
        ]);
        $deliveryFieldset->addField('recipient_address', 'textarea', [
            'name'  => 'recipient_address',
            'label' => __('Address'),
            'title' => __('Address')
        ]);
        $deliveryFieldset->addField('delivery_date', 'date', [
            'name'        => 'delivery_date',
            'label'       => __('Delivery Date'),
            'title'       => __('Delivery Date'),
            'class'       => 'validate-date',
            'date_format' => $this->_localeDate->getDateFormat(IntlDateFormatter::MEDIUM)
        ]);
        $deliveryFieldset->addField('timezone', 'select', [
            'name'   => 'timezone',
            'label'  => __('Timezone'),
            'title'  => __('Timezone'),
            'values' => $this->timezoneSource->toOptionArray()
        ]);
        if (!$model->getTimezone()) {
            $model->setTimezone($this->_templateHelper->getConfigValue(
                Data::XML_PATH_DEFAULT_TIMEZONE,
                $model->getStoreId()
            ));
        }

        $dependencies->addFieldMap('delivery_method', 'delivery_method')
            ->addFieldMap('recipient_email', 'recipient_email')
            ->addFieldMap('recipient_phone', 'recipient_phone')
            ->addFieldMap('customer_email', 'customer_email')
            ->addFieldMap('recipient_address', 'recipient_address')
            ->addFieldMap('delivery_date', 'delivery_date')
            ->addFieldMap('timezone', 'timezone')
            ->addFieldDependence('recipient_email', 'delivery_method', DeliveryMethods::METHOD_EMAIL)
            ->addFieldDependence('recipient_phone', 'delivery_method', DeliveryMethods::METHOD_SMS)
            ->addFieldDependence('customer_email', 'delivery_method', DeliveryMethods::METHOD_PRINT)
            ->addFieldDependence('recipient_address', 'delivery_method', DeliveryMethods::METHOD_POST)
            ->addFieldDependence(
                'delivery_date',
                'delivery_method',
                $this->_templateHelper->getRefField([
                    DeliveryMethods::METHOD_EMAIL,
                    DeliveryMethods::METHOD_SMS,
                    DeliveryMethods::METHOD_POST
                ])
            )->addFieldDependence(
                'timezone',
                'delivery_method',
                $this->_templateHelper->getRefField([
                    DeliveryMethods::METHOD_EMAIL,
                    DeliveryMethods::METHOD_SMS,
                    DeliveryMethods::METHOD_POST
                ])
            );

        $model->setData(
            DeliveryMethods::getFormFieldName($model->getDeliveryMethod()),
            $model->getDeliveryAddress()
        );

        // define field dependencies
        $this->setChild('form_after', $dependencies);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Delivery Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Delivery Information');
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
}

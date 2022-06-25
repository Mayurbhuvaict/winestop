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

namespace Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab\Generate;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\Source\GenerateType as GenType;

/**
 * Class Information
 * @package Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab
 */
class Form extends Generic
{
    /**
     * @var GenType
     */
    private $generateType;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param GenType $generateType
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        GenType $generateType,
        array $data = []
    ) {
        $this->generateType = $generateType;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare coupon codes generation parameters form
     *
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /* @var $model Pool */
        $model = $this->_coreRegistry->registry('current_pool');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('pool_');

        $fieldset = $form->addFieldset('generate_fieldset', ['legend' => __('Pool Information')]);
        $fieldset->addClass('ignore-validate');

        $fieldset->addField('generate_type', 'select', [
            'name'   => 'generate_type',
            'label'  => __('Generate Type'),
            'title'  => __('Generate Type'),
            'values' => $this->generateType->toOptionArray(),
        ]);

        $fieldset->addField('sample_file', 'link', [
            'name'   => 'sample_file',
            'label'  => '',
            'title'  => __('Download Sample File'),
            'href'   => $this->getUrl('*/*/downloadSample'),
            'value'  => __('Download Sample File'),
            'target' => '_blank'
        ]);

        $fieldset->addField('import_file', 'file', [
            'name'     => 'import_file',
            'label'    => __('Select File'),
            'title'    => __('Select File'),
            'required' => true,
            'class'    => 'input-file',
            'note'     => __('Only support file .csv'),
        ]);

        $fieldset->addField('import_button', 'note', [
            'name' => 'import_button',
            'text' => $this->getButtonHtml(
                __('Import'),
                "mpImportPool('pool_' ,'{$this->getUrl('*/*/importCsv')}')"
            )
        ]);

        $fieldset->addField('manual_code', 'textarea', [
            'name'     => 'manual_code',
            'label'    => __('Gift Code(s)'),
            'title'    => __('Gift Code(s)'),
            'required' => true,
            'note'     => __('Separated by line breaks')
        ]);

        $fieldset->addField('manual_button', 'note', [
            'name' => 'manual_button',
            'text' => $this->getButtonHtml(
                __('Add'),
                "mpAddPool('pool_' ,'{$this->getUrl('*/*/manualAdd')}')"
            )
        ]);

        $fieldset->addField('pattern', 'text', [
            'name'     => 'pattern',
            'label'    => __('Code Pattern'),
            'title'    => __('Code Pattern'),
            'value'    => $model->getPattern(),
            'required' => true
        ]);

        $fieldset->addField('qty', 'text', [
            'name'     => 'qty',
            'label'    => __('Gift Card Qty'),
            'title'    => __('Gift Card Qty'),
            'required' => true,
            'class'    => 'validate-digits validate-greater-than-zero'
        ]);

        $fieldset->addField('generate_button', 'note', [
            'name' => 'generate_button',
            'text' => $this->getButtonHtml(
                __('Generate'),
                "mpGeneratePool('pool_' ,'{$this->getUrl('*/*/generate')}')",
                'generate'
            )
        ]);

        // define field dependencies
        $this->setChild('form_after', $this->addDependencies());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return Dependence
     * @throws LocalizedException
     */
    private function addDependencies()
    {
        /** @var Dependence $dependencies */
        $dependencies = $this->getLayout()->createBlock(Dependence::class);

        $dependencies->addFieldMap('pool_generate_type', 'generate_type')
            ->addFieldMap('pool_import_file', 'import_file')
            ->addFieldMap('pool_sample_file', 'sample_file')
            ->addFieldMap('pool_import_button', 'import_button')
            ->addFieldMap('pool_manual_code', 'manual_code')
            ->addFieldMap('pool_manual_button', 'manual_button')
            ->addFieldMap('pool_pattern', 'pattern')
            ->addFieldMap('pool_qty', 'qty')
            ->addFieldMap('pool_generate_button', 'generate_button')
            ->addFieldDependence('import_file', 'generate_type', GenType::IMPORT)
            ->addFieldDependence('sample_file', 'generate_type', GenType::IMPORT)
            ->addFieldDependence('import_button', 'generate_type', GenType::IMPORT)
            ->addFieldDependence('manual_code', 'generate_type', GenType::MANUAL)
            ->addFieldDependence('manual_button', 'generate_type', GenType::MANUAL)
            ->addFieldDependence('pattern', 'generate_type', GenType::AUTO)
            ->addFieldDependence('qty', 'generate_type', GenType::AUTO)
            ->addFieldDependence('generate_button', 'generate_type', GenType::AUTO);

        return $dependencies;
    }
}

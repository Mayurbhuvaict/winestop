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

namespace Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\Source\Fonts;
use Mageplaza\GiftCard\Model\Source\Status;
use Mageplaza\GiftCard\Model\Template;

/**
 * Class Information
 * @package Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab
 */
class Information extends Generic implements TabInterface
{
    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var Yesno
     */
    protected $_yesno;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var Fonts
     */
    protected $_fonts;

    /**
     * Information constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Status $statusOptions
     * @param Yesno $yesno
     * @param Fonts $fonts
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $statusOptions,
        Yesno $yesno,
        Fonts $fonts,
        Data $dataHelper,
        array $data = []
    ) {
        $this->_status     = $statusOptions;
        $this->_yesno      = $yesno;
        $this->_fonts      = $fonts;
        $this->_dataHelper = $dataHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /* @var $model Template */
        $model = $this->_coreRegistry->registry('current_template');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Template Information')]);

        if ($model->getId()) {
            $fieldset->addField('template_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Template Name'),
            'title'    => __('Template Name'),
            'required' => true
        ]);
        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->_status->toOptionArray()
        ]);
        $fieldset->addField('can_upload', 'select', [
            'name'   => 'can_upload',
            'label'  => __('Allow Upload Image'),
            'title'  => __('Allow Upload Image'),
            'values' => $this->_yesno->toOptionArray()
        ]);
        $fieldset->addField('title', 'text', [
            'name'     => 'title',
            'label'    => __('Gift Card Title'),
            'title'    => __('Gift Card Title'),
            'note'     => 'The title of Gift Cards which using this template.',
            'required' => true
        ]);
        $fieldset->addField('font_family', 'select', [
            'name'     => 'font_family',
            'label'    => __('Font Family'),
            'title'    => __('Font Family'),
            'required' => true,
            'values'   => $this->_fonts->toOptionArray()
        ]);
        $fieldset->addField('background_image', 'image', [
            'name'  => 'background_image',
            'label' => __('Background Image'),
            'title' => __('Background Image')
        ]);
        $fieldset->addField('note', 'textarea', [
            'name'  => 'note',
            'label' => __('Gift Card Note'),
            'title' => __('Gift Card Note')
        ]);

        if (!$model->getId()) {
            $model->addData([
                'status'      => Status::STATUS_ACTIVE,
                'title'       => __('Gift Card'),
                'font_family' => 'pdfatimes',
                'note'        => $this->_dataHelper->getTemplateConfig('note')
            ]);
        }

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
        return __('Template Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Template Information');
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

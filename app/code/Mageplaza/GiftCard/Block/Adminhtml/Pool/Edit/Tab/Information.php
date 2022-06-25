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

use IntlDateFormatter;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\Source\Status;

/**
 * Class Information
 * @package Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab
 */
class Information extends Generic implements TabInterface
{
    /** @var Store */
    protected $_systemStore;

    /** @var Yesno */
    protected $_yesnoOptions;

    /** @var DataHelper */
    protected $_helper;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * Information constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Yesno $yesnoOptions
     * @param DataHelper $helper
     * @param Status $statusOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Yesno $yesnoOptions,
        DataHelper $helper,
        Status $statusOptions,
        array $data = []
    ) {
        $this->_systemStore  = $systemStore;
        $this->_yesnoOptions = $yesnoOptions;
        $this->_helper       = $helper;
        $this->_status       = $statusOptions;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /* @var $model Pool */
        $model = $this->_coreRegistry->registry('current_pool');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $informationFieldset = $form->addFieldset('information_fieldset', ['legend' => __('Pool Information')]);

        if ($model->getId()) {
            $informationFieldset->addField('pool_id', 'hidden', ['name' => 'id']);
        }

        $informationFieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);
        $informationFieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->_status->toOptionArray(),
            'note'   => __('The gift cards of this pool cannot be used if the pool status is inactive.')
        ]);

        $informationFieldset->addField('can_inherit', 'select', [
            'name'   => 'can_inherit',
            'label'  => __('Can Inherit'),
            'title'  => __('Can Inherit'),
            'values' => $this->_yesnoOptions->toOptionArray(),
            'note'   => __('If yes, when you change this pool information, the gift card information will be changed. Except status attribute')
        ]);

        $giftCardFieldset = $form->addFieldset('gift_card_fieldset', ['legend' => __('Gift Card Information')]);

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeManager->getStore();
        $giftCardFieldset->addType('price', Price::class);
        $giftCardFieldset->addField('balance', 'price', [
            'name'     => 'balance',
            'label'    => __('Balance'),
            'title'    => __('Balance'),
            'class'    => 'validate-greater-than-zero',
            'required' => true
        ])->setAfterElementHtml($store->getBaseCurrency()->getCurrencySymbol());

        if ($this->_helper->getGeneralConfig('enable_credit')) {
            $giftCardFieldset->addField('can_redeem', 'select', [
                'name'   => 'can_redeem',
                'label'  => __('Can Redeem'),
                'title'  => __('Can Redeem'),
                'values' => $this->_yesnoOptions->toOptionArray()
            ]);
            if (!$model->getId()) {
                $model->setData('can_redeem', $this->_helper->getGeneralConfig('can_redeem'));
            }
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $giftCardFieldset->addField('store_id', 'select', [
                'name'   => 'store_id',
                'label'  => __('Store'),
                'title'  => __('Store'),
                'values' => $this->_systemStore->getStoreValuesForForm()
            ])->setRenderer($rendererBlock);
        }

        $giftCardFieldset->addField('expired_at', 'date', [
            'name'        => 'expired_at',
            'label'       => __('Expires At'),
            'title'       => __('Expires At'),
            'class'       => 'validate-date',
            'date_format' => $this->_localeDate->getDateFormat(IntlDateFormatter::MEDIUM)
        ]);

        /** @var Dependence $dependencies */
        $dependencies = $this->getLayout()->createBlock(Dependence::class);

        /** Add template fields: image, sender name, recipient name, message */
        $this->_helper->getTemplateHelper()->getTemplateFieldSet($model, $giftCardFieldset, $dependencies);

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
        return __('Gift Code Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Gift Code Information');
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

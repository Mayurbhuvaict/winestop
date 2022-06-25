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
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\System\Store;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\PoolFactory;

/**
 * Class Information
 * @package Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab
 */
class Information extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_pricingCurrency;

    /**
     * @var Yesno
     */
    protected $_yesnoOptions;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var PoolFactory
     */
    private $poolFactory;

    /**
     * Information constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param PriceCurrencyInterface $pricingCurrency
     * @param Yesno $yesnoOptions
     * @param Data $helper
     * @param OrderFactory $orderFactory
     * @param ItemFactory $itemFactory
     * @param PoolFactory $poolFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        PriceCurrencyInterface $pricingCurrency,
        Yesno $yesnoOptions,
        Data $helper,
        OrderFactory $orderFactory,
        ItemFactory $itemFactory,
        PoolFactory $poolFactory,
        array $data = []
    ) {
        $this->_systemStore     = $systemStore;
        $this->_pricingCurrency = $pricingCurrency;
        $this->_yesnoOptions    = $yesnoOptions;
        $this->_helper          = $helper;
        $this->orderFactory     = $orderFactory;
        $this->itemFactory      = $itemFactory;
        $this->poolFactory      = $poolFactory;

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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card Information')]);
        $fieldset->addType('price', Price::class);

        if ($model->getId()) {
            $fieldset->addField('giftcard_id', 'hidden', ['name' => 'id']);
            $fieldset->addField('code', 'label', [
                'label' => __('Gift Code'),
                'title' => __('Gift Code'),
                'name'  => 'code'
            ]);
        } else {
            $fieldset->addField('pattern', 'text', [
                'name'     => 'pattern',
                'label'    => __('Code Pattern'),
                'title'    => __('Code Pattern'),
                'required' => true
            ]);
        }

        $fieldset->addField('balance', 'price', [
            'name'     => 'balance',
            'label'    => __('Balance'),
            'title'    => __('Balance'),
            'class'    => 'validate-greater-than-zero',
            'required' => true
        ])->setAfterElementHtml('$');

        $status = Status::getOptionArrayForForm();
        if (!$model->getId() || array_key_exists($model->getStatus(), $status)) {
            $fieldset->addField('status', 'select', [
                'name'   => 'status',
                'label'  => __('Status'),
                'title'  => __('Status'),
                'values' => $status
            ]);
            if (!$model->getId()) {
                $model->setData('status', Status::STATUS_ACTIVE);
            }
        } else {
            $fieldset->addField('status', 'note', [
                'label' => __('Status'),
                'text'  => $model->getStatusLabel()
            ]);
        }

        if ($this->_helper->getGeneralConfig('enable_credit')) {
            $fieldset->addField('can_redeem', 'select', [
                'name'   => 'can_redeem',
                'label'  => __('Is Redeemable'),
                'title'  => __('Is Redeemable'),
                'values' => $this->_yesnoOptions->toOptionArray()
            ]);
            if (!$model->getId()) {
                $model->setData('can_redeem', $this->_helper->getGeneralConfig('can_redeem'));
            }
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_id', 'select', [
                'name'   => 'store_id',
                'label'  => __('Store'),
                'title'  => __('Store'),
                'values' => $this->_systemStore->getStoreValuesForForm()
            ])->setRenderer($rendererBlock);
        }

        $fieldset->addField('expired_at', 'date', [
            'name'        => 'expired_at',
            'label'       => __('Expires At'),
            'title'       => __('Expires At'),
            'class'       => 'validate-date',
            'date_format' => $this->_localeDate->getDateFormat(IntlDateFormatter::MEDIUM)
        ]);

        if ($model->getId()) {
            $createdFrom = '';
            if ($incrementId = $model->getOrderIncrementId()) {
                /** @var Order $order */
                $order       = $this->orderFactory->create()->loadByIncrementId($incrementId);
                $orderUrl    = $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
                $createdFrom .= __('Order: %1', '<a href="' . $orderUrl . '">#' . $incrementId . '</a>');
                if ($itemId = $model->getOrderItemId()) {
                    /** @var Item $product */
                    $product     = $this->itemFactory->create()->load($itemId)->getProduct();
                    $productUrl  = $this->getUrl('catalog/product/edit', ['id' => $product->getId()]);
                    $createdFrom .= '<br />';
                    $createdFrom .= __('Product: %1', '<a href="' . $productUrl . '">#' . $product->getName() . '</a>');
                }
            } elseif ($poolId = $model->getPoolId()) {
                /** @var Pool $pool */
                $pool = $this->poolFactory->create()->load($poolId);
                if ($pool->getId()) {
                    $createdFrom .= __('Pool: %1', $pool->getName());
                }
            } elseif ($extraContent = $model->getExtraContent()) {
                if (isset($extraContent['auth'])) {
                    $createdFrom .= __('Admin: %1', $extraContent['auth']);
                }
            }
            if ($createdFrom) {
                $fieldset->addField('created_from', 'note', [
                    'label' => __('Created From'),
                    'text'  => $createdFrom
                ]);
            }
            $fieldset->addField('created_at', 'note', [
                'label' => __('Created At'),
                'text'  => $this->_localeDate->formatDateTime($model->getCreatedAt(), IntlDateFormatter::MEDIUM)
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

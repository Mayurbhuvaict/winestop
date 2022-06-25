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

namespace Mageplaza\GiftCard\Block\Adminhtml\GiftCard;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Helper\Template;

/**
 * Class Edit
 * @package Mageplaza\GiftCard\Block\Adminhtml\GiftCard
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * @var Template
     */
    protected $templateHelper;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Template $templateHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Template $templateHelper,
        array $data = []
    ) {
        $this->_coreRegistry  = $coreRegistry;
        $this->templateHelper = $templateHelper;
        parent::__construct($context, $data);
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Mageplaza_GiftCard';
        $this->_controller = 'adminhtml_giftCard';

        parent::_construct();

        $giftCard = $this->_coreRegistry->registry('current_giftcard');
        if ($giftCard && $giftCard->getId() && $giftCard->getTemplateId()) {
            $this->buttonList->add(
                'print',
                [
                    'label'          => __('Print'),
                    'class'          => 'save',
                    'data_attribute' => [
                        'mage-init' => ['button' => ['event' => 'print', 'target' => '#edit_form']]
                    ]
                ]
            );
        }

        $this->buttonList->add(
            'saveandsend',
            [
                'label'          => __('Save and Send'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndSend', 'target' => '#edit_form']]
                ]
            ]
        );

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ]
        );

        $this->_formScripts[] = $this->templateHelper->getFormScript();
        $this->_formScripts[] = "
            require(['jquery', 'mage/mage', 'mage/backend/form'], function ($) {
                $('#edit_form').mage('form', {
                    handlersData: {
                        saveAndSend: {
                            action: {
                                args: {action: 'send'}
                            }
                        },
                        print: {
                            action: {
                                args: {action: 'print'}
                            }
                        }
                    }
                });
            });";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $currentGiftCard = $this->_coreRegistry->registry('current_giftcard');
        if ($currentGiftCard && $currentGiftCard->getId()) {
            return __("Edit Gift Card '%1'", $this->escapeHtml($currentGiftCard->getCode()));
        }

        return __('New Gift Card');
    }
}

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

namespace Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\Condition;
use Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\History;
use Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\Information;
use Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit\Tab\Send;

/**
 * Class Tabs
 * @package Mageplaza\GiftCard\Block\Adminhtml\GiftCard\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Tabs constructor.
     *
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('giftcard_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gift Card Information'));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('information', Information::class);
        $this->addTab('conditions', Condition::class);
        $this->addTab('send', Send::class);

        $giftCard = $this->_coreRegistry->registry('current_giftcard');
        if ($giftCard->getId()) {
            $this->addTab('giftcard_history_grid', History::class);
        }
    }
}

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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCard\Status as CardStatus;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\Source\Status as PoolStatus;

/**
 * Class Edit
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Code
 */
class Edit extends Code
{
    /** @var Registry */
    protected $registry;

    /** @var Data */
    protected $_giftcardHelper;

    /** @var PoolFactory */
    protected $_poolFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     * @param PoolFactory $poolFactory
     * @param Registry $registry
     * @param Data $giftcardHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory,
        PoolFactory $poolFactory,
        Registry $registry,
        Data $giftcardHelper
    ) {
        $this->registry        = $registry;
        $this->_giftcardHelper = $giftcardHelper;
        $this->_poolFactory    = $poolFactory;

        parent::__construct($context, $resultPageFactory, $giftCardFactory);
    }

    /**
     * @return Page|Redirect
     */
    public function execute()
    {
        $giftCard = $this->_initObject();
        if (!$giftCard) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        if ($giftCard->isObjectNew()) {
            $giftCard->setPattern($this->_giftcardHelper->getCodePattern());
        }
        //Set entered data if was error when we do save
        $data = $this->_session->getData('giftcard_code_form', true);
        if (!empty($data)) {
            $giftCard->addData($data);
        }

        $this->registry->register('current_giftcard', $giftCard);

        if ($poolId = $giftCard->getPoolId()) {
            $pool = $this->_poolFactory->create()->load($poolId);
            if ((int) $pool->getStatus() === PoolStatus::STATUS_INACTIVE
                && (int) $giftCard->getStatus() === CardStatus::STATUS_ACTIVE) {
                $this->messageManager->addNoticeMessage(__('This code is disabled by Pool "%1"', $pool->getName()));
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend($giftCard->getId() ? __('Edit Gift Code') : __('Create Gift Code'));

        return $resultPage;
    }
}

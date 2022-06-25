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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Pool;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Helper\Data as GiftCardHelper;
use Mageplaza\GiftCard\Model\PoolFactory;

/**
 * Class Edit
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class Edit extends Pool
{
    /** @var Registry */
    protected $registry;

    /** @var GiftCardHelper */
    protected $_giftCardHelper;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param Registry $registry
     * @param GiftCardHelper $giftCardHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        Registry $registry,
        GiftCardHelper $giftCardHelper
    ) {
        $this->registry        = $registry;
        $this->_giftCardHelper = $giftCardHelper;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * @return Page|Redirect
     */
    public function execute()
    {
        $pool = $this->_initObject();
        if (!$pool) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        if (!$pool->getPattern()) {
            $pool->setPattern($this->_giftCardHelper->getCodePattern());
        }

        //Set entered data if was error when we do save
        $data = $this->_session->getData('pool_form_data', true);
        if (!empty($data)) {
            $pool->addData($data);
        }

        $this->registry->register('current_pool', $pool);

        /** @var Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend($pool->getId() ? __('Edit Code Pool \'#' . $pool->getId() . '\'') : __('Create New Code Pool'));

        return $resultPage;
    }
}

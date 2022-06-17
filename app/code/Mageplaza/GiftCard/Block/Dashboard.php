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

namespace Mageplaza\GiftCard\Block;

use Exception;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Mageplaza\GiftCard\Helper\Customer;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\CreditFactory;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\Transaction;

/**
 * Class Dashboard
 * @package Mageplaza\GiftCard\Block
 */
class Dashboard extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CreditFactory
     */
    protected $_creditFactory;

    /**
     * @var Customer
     */
    protected $giftCardHelper;

    /**
     * @var Transaction
     */
    protected $_transaction;

    /**
     * @var GiftCard
     */
    protected $_giftCard;

    /**
     * Dashboard constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CreditFactory $creditFactory
     * @param DataHelper $giftCardHelper
     * @param Transaction $transaction
     * @param GiftCard $giftCard
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CreditFactory $creditFactory,
        DataHelper $giftCardHelper,
        Transaction $transaction,
        GiftCard $giftCard,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->_creditFactory  = $creditFactory;
        $this->giftCardHelper  = $giftCardHelper;
        $this->_transaction    = $transaction;
        $this->_giftCard       = $giftCard;

        parent::__construct($context, $data);
    }

    /**
     * Returns popup config
     *
     * @return array
     * @throws Exception
     * @throws NoSuchEntityException
     */
    public function getConfig()
    {
        $customer = $this->customerSession->getCustomer();
        if (!$customer || !$customer->getId()) {
            return [];
        }

        $emailEnable       = $this->giftCardHelper->getEmailConfig('enable');
        $creditEmailEnable = $this->giftCardHelper->getEmailConfig('credit/enable');

        $creditAccount = $this->_creditFactory->create()
            ->load($customer->getId(), 'customer_id');
        $code          = $this->getRequest()->getParam('code');

        return [
            'baseUrl'        => $this->getBaseUrl(),
            'customerEmail'  => $customer->getEmail(),
            'code'           => $code,
            'balance'        => $this->giftCardHelper->getCustomerBalance($customer, true, true),
            'transactions'   => $this->_transaction->getTransactionsForCustomer($customer->getId()),
            'giftCardLists'  => $this->_giftCard->getGiftCardListForCustomer($customer->getId()),
            'isEnableCredit' => (bool) $this->giftCardHelper->getGeneralConfig('enable_credit'),
            'notification'   => [
                'enable'               => $emailEnable,
                'creditEnable'         => $creditEmailEnable,
                'creditNotification'   => $creditAccount->getCreditNotification() === null
                    ? true : (boolean) $creditAccount->getCreditNotification(),
                'giftcardNotification' => $creditAccount->getGiftcardNotification() === null
                    ? true : (boolean) $creditAccount->getGiftcardNotification()
            ]
        ];
    }
}

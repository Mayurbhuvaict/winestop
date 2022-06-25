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

namespace Mageplaza\GiftCard\Controller\Index;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonDataHelper;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Transaction;
use Mageplaza\GiftCard\Model\TransactionFactory;

/**
 * Class Check
 * @package Magento\Customer\Controller\Ajax
 */
class Redeem extends Action
{
    /**
     * @var JsonDataHelper $helper
     */
    protected $helper;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var DataHelper
     */
    protected $giftCardHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Redeem constructor.
     *
     * @param Context $context
     * @param JsonDataHelper $helper
     * @param JsonFactory $resultJsonFactory
     * @param RawFactory $resultRawFactory
     * @param GiftCardFactory $giftCardFactory
     * @param TransactionFactory $transactionFactory
     * @param DataHelper $giftCardHelper
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        JsonDataHelper $helper,
        JsonFactory $resultJsonFactory,
        RawFactory $resultRawFactory,
        GiftCardFactory $giftCardFactory,
        TransactionFactory $transactionFactory,
        DataHelper $giftCardHelper,
        Session $customerSession
    ) {
        $this->helper             = $helper;
        $this->resultJsonFactory  = $resultJsonFactory;
        $this->giftCardFactory    = $giftCardFactory;
        $this->transactionFactory = $transactionFactory;
        $this->resultRawFactory   = $resultRawFactory;
        $this->customerSession    = $customerSession;
        $this->giftCardHelper     = $giftCardHelper;

        parent::__construct($context);
    }

    /**
     * Login registered users and initiate a session.
     *
     * Expects a POST. ex for JSON {"username":"user@magento.com", "password":"password"}
     *
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $credentials        = null;
        $httpBadRequestCode = 400;

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        if (!$this->giftCardHelper->isEnabled() || !$this->getRequest()->isAjax()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors'  => false,
            'message' => __('Redeeming Gift Card "%1" successfully.', $credentials['code'])
        ];
        try {
            /** @var GiftCard $giftCard */
            $giftCard = $this->giftCardFactory->create();
            $giftCard->load($credentials['code'], 'code');

            if (!$giftCard->canRedeem()) {
                throw new LocalizedException(__('Gift Card "%1" cannot be redeemed.', $giftCard->getCode()));
            }

            $customer = $this->customerSession->getCustomer();

            /** @var Transaction $transaction */
            $transaction = $this->transactionFactory->create()->redeemGiftCard($customer, $giftCard);

            $response['balance']      = $this->giftCardHelper->getCustomerBalance($customer, true, true);
            $response['transactions'] = $transaction->getTransactionsForCustomer($customer->getId());

            $customerIds = $giftCard->getCustomerIds() ? explode(',', $giftCard->getCustomerIds()) : [];
            if (count($customerIds) && in_array((string) $customer->getId(), $customerIds, true)) {
                $response['giftCardLists'] = $giftCard->getGiftCardListForCustomer($customer->getId());
            }
        } catch (LocalizedException $e) {
            $response = [
                'errors'  => true,
                'message' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $response = [
                'errors'  => true,
                'message' => __('Invalid gift card code.' . $e->getMessage())
            ];
        }
        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($response);
    }
}

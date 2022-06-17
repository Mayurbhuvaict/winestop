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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Customer;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\Transaction;
use Mageplaza\GiftCard\Model\Transaction\Action as TransactionAction;
use Mageplaza\GiftCard\Model\TransactionFactory;

/**
 * Class Change
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Customer
 */
class Change extends Action
{
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * Change constructor.
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param JsonFactory $resultJsonFactory
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        JsonFactory $resultJsonFactory,
        TransactionFactory $transactionFactory
    ) {
        $this->_dataHelper         = $dataHelper;
        $this->_resultJsonFactory  = $resultJsonFactory;
        $this->_transactionFactory = $transactionFactory;

        parent::__construct($context);
    }

    /**
     * Execute - Change Customer balance amount
     *
     * @return ResponseInterface|Json|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $result  = ['error' => true];
        $request = $this->getRequest();
        if ($request->getParam('isAjax')) {
            $customerId = $request->getParam('customer_id');
            $customer   = $this->_dataHelper->getCustomer($customerId);
            $amount     = $request->getParam('amount');
            $currency   = $customer->getStore()->getBaseCurrency();

            /** @var Transaction $transaction */
            $transaction = $this->_transactionFactory->create();

            try {
                $transaction->createTransaction(
                    TransactionAction::ACTION_ADMIN,
                    $amount,
                    $customer,
                    ['auth' => $this->_auth->getUser()->getName()]
                );

                $balance = $this->_dataHelper->getCustomerBalance($customerId, false);
                $result  = [
                    'error'            => false,
                    'balance'          => $balance,
                    'balanceFormatted' => $this->_dataHelper->formatPrice($balance, true, null, $currency)
                ];
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
            }
        } else {
            $result['message'] = __('An error occur. Please try again later.');
        }

        return $this->_resultJsonFactory->create()->setData($result);
    }
}

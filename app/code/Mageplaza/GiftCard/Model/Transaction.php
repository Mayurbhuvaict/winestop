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

namespace Mageplaza\GiftCard\Model;

use Exception;
use IntlDateFormatter;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Helper\Email;
use Mageplaza\GiftCard\Model\GiftCard\Action as GiftCardAction;
use Mageplaza\GiftCard\Model\Transaction\Action;

/**
 * Class Transaction
 * @package Mageplaza\GiftCard\Model
 * @method getAction()
 */
class Transaction extends AbstractModel implements IdentityInterface
{
    /**
     * Cache
     */
    const CACHE_TAG = 'mageplaza_giftcard_transaction';

    /**
     * @var CreditFactory
     */
    protected $creditFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CreditFactory $creditFactory
     * @param CustomerFactory $customerFactory
     * @param DataHelper $dataHelper
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CreditFactory $creditFactory,
        CustomerFactory $customerFactory,
        DataHelper $dataHelper,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->creditFactory   = $creditFactory;
        $this->customerFactory = $customerFactory;
        $this->_dataHelper     = $dataHelper;
        $this->_storeManager   = $storeManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Transaction::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get gift card transaction action label
     *
     * @param null $action
     *
     * @return Phrase|string
     */
    public function getActionLabel($action = null)
    {
        if ($action === null) {
            $action = $this->getAction();
        }

        $allStatus = Action::getOptionArray();

        return isset($allStatus[$action]) ? $allStatus[$action] : __('Undefined');
    }

    /**
     * @param $action
     * @param $amount
     * @param $customer
     * @param $extraContent
     *
     * @return Credit
     * @throws LocalizedException
     */
    public function createTransaction($action, $amount, $customer, $extraContent)
    {
        $credit = $this->prepareTransaction($action, $amount, $customer, $extraContent);

        $this->getResource()->createTransaction([$this, $credit]);

        return $credit;
    }

    /**
     * @param Customer $customer
     * @param GiftCard $giftCard
     *
     * @return $this
     * @throws LocalizedException
     */
    public function redeemGiftCard($customer, $giftCard)
    {
        if (!$giftCard->isActive()) {
            throw new LocalizedException(__(
                'Cannot redeem gift card. Gift Card "%1" does not available.',
                $giftCard->getCode()
            ));
        }

        $credit = $this->prepareTransaction(
            Action::ACTION_REDEEM,
            $giftCard->getBalance(),
            $customer,
            ['code' => $giftCard->getCode()]
        );

        $giftCard->setBalance(0)
            ->setActionVars(['auth' => $customer->getName(), 'customer_id' => $customer->getId()])
            ->setAction(GiftCardAction::ACTION_REDEEM);

        $this->getResource()->createTransaction([$this, $credit, $giftCard]);

        return $this;
    }

    /**
     * @param $action
     * @param $amount
     * @param Customer|int $customer
     * @param $extraContent
     *
     * @return Credit
     * @throws LocalizedException
     */
    protected function prepareTransaction($action, $amount, &$customer, $extraContent)
    {
        if (is_numeric($customer)) {
            $customer = $this->customerFactory->create()->load($customer);
        }
        if (!$customer->getId()) {
            throw new LocalizedException(__('Customer does not exists.'));
        }

        $credit = $this->creditFactory->create()->load($customer->getId(), 'customer_id');
        if (!$credit->getId()) {
            try {
                $credit->setCustomerId($customer->getId())->save();
            } catch (Exception $e) {
                throw new LocalizedException(__('Cannot save customer balance.'));
            }
        }

        $balanceAfterChange = $credit->getBalance() + $amount;
        if ($balanceAfterChange < 0) {
            throw new LocalizedException(__('Customer balance is not enough.'));
        }

        $credit->setBalance($balanceAfterChange);

        //Prepare information for send mail to customer that used balance to place order
        if ((int) $action === Action::ACTION_SPEND) {
            $credit->setAmount($amount);
            $credit->setAction(Action::ACTION_SPEND);
            $credit->setOrderIncrementId($extraContent['order_increment_id']);
            $credit->setCustomer($customer);
        }

        $this->setData([
            'credit'        => $credit,
            'credit_id'     => $credit->getId(),
            'balance'       => $balanceAfterChange,
            'amount'        => $amount,
            'action'        => $action,
            'extra_content' => DataHelper::jsonEncode($extraContent)
        ]);

        return $credit;
    }

    /**
     * Get Customer transaction
     *
     * @param $customerId
     *
     * @return array
     * @throws Exception
     */
    public function getTransactionsForCustomer($customerId)
    {
        $transactionList = [];

        $transactions = $this->getCollection()->setOrder('created_at', 'desc');
        $transactions->getSelect()
            ->join(
                ['cr' => $transactions->getTable('mageplaza_giftcard_credit')],
                'main_table.credit_id = cr.credit_id AND cr.customer_id = ' . $customerId,
                ['customer_id']
            );

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $transaction->addData([
                'created_at_formatted' => $this->_dataHelper->formatDate(
                    $transaction->getCreatedAt(),
                    IntlDateFormatter::MEDIUM
                ),
                'action_label'         => $transaction->getActionLabel(),
                'amount_formatted'     => $this->_dataHelper->convertPrice($transaction->getAmount()),
                'action_detail'        => Action::getActionLabel(
                    $transaction->getAction(),
                    $transaction->getExtraContent()
                )
            ]);

            $transactionList[] = $transaction->getData();
        }

        return $transactionList;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function afterSave()
    {
        parent::afterSave();

        /** @var Email $emailHelper */
        $emailHelper = $this->_dataHelper->getEmailHelper();
        if ($emailHelper->isEmailEnable(Email::EMAIL_TYPE_CREDIT)) {
            $credit = $this->getCredit();

            $notification = $credit->getCreditNotification() === null
                ? true : (boolean) $credit->getCreditNotification();
            if (!$notification) {
                return $this;
            }

            $customer = $this->customerFactory->create()->load($credit->getCustomerId());
            if (!$customer || !$customer->getId()) {
                return $this;
            }

            $emailHelper->sendEmailTemplate(
                Email::EMAIL_TYPE_CREDIT,
                $customer->getName(),
                $customer->getEmail(),
                [
                    'customer'         => $customer,
                    'title'            => Action::getActionLabel($this->getAction(), $this->getExtraContent()),
                    'credit_amount'    => $this->_dataHelper->convertPrice($this->getAmount(), true, false),
                    'customer_balance' => $this->_dataHelper->convertPrice($credit->getBalance(), true, false)
                ]
            );
        }

        return $this;
    }
}

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

use DateInterval;
use DateTime;
use Exception;
use IntlDateFormatter;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date as DateFilter;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Model\Order;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GiftCard\Api\Data\GiftCodeInterface;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Helper\Email;
use Mageplaza\GiftCard\Model\GiftCard\Action;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\Product\DeliveryMethods;
use Mageplaza\GiftCard\Model\ResourceModel\History\Collection;
use Mageplaza\GiftCard\Model\Source\Status as GcStatus;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Twilio\Exceptions\ConfigurationException;

/**
 * Class GiftCard
 * @package Mageplaza\GiftCard\Model
 *
 * @method getActionVars()
 * @method GiftCard setAction(int $value)
 * @method GiftCard setActionVars(array $value)
 * @method getExpireAfter()
 * @method getConditionsSerialized()
 */
class GiftCard extends AbstractModel implements IdentityInterface, GiftCodeInterface
{
    const CACHE_TAG = 'mageplaza_giftcard';

    /**
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_giftcard';

    /**
     * @var bool Is gc active
     */
    protected $_isActive;

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * @var HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @var Random
     */
    protected $_mathRandom;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PoolFactory
     */
    protected $_poolFactory;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Address
     */
    private $addressToValidate;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var DateFilter
     */
    protected $dateFilter;

    /**
     * GiftCard constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $dataHelper
     * @param HistoryFactory $historyFactory
     * @param Random $mathRandom
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     * @param PoolFactory $poolFactory
     * @param RuleFactory $ruleFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Http $request
     * @param DateFilter $dateFilter
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $dataHelper,
        HistoryFactory $historyFactory,
        Random $mathRandom,
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        PoolFactory $poolFactory,
        RuleFactory $ruleFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Http $request,
        DateFilter $dateFilter,
        array $data = []
    ) {
        $this->_helper          = $dataHelper;
        $this->_historyFactory  = $historyFactory;
        $this->_mathRandom      = $mathRandom;
        $this->_customerFactory = $customerFactory;
        $this->storeManager     = $storeManager;
        $this->_poolFactory     = $poolFactory;
        $this->ruleFactory      = $ruleFactory;
        $this->request          = $request;
        $this->dateFilter       = $dateFilter;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\GiftCard::class);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     * @throws Exception
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if ($this->getBalance() < 0) {
            throw new LocalizedException(__('Balance must be greater than or equal zero.'));
        }

        if ($this->isObjectNew()) {
            $this->setCode($this->generateCode());
            $this->setAction(Action::ACTION_CREATE)->setInitBalance($this->getBalance());
        } elseif (!$this->hasAction() &&
            (($this->getData('balance') !== $this->getOrigData('balance')) ||
                ($this->getData('status') !== $this->getOrigData('status')))
        ) {
            $this->setAction(Action::ACTION_UPDATE);
        }

        $this->processExpiredDate();
        $this->processStatus();

        if ($this->getData('send_to_recipient')) {
            $this->setData('is_sent', true);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public function addBalance($amount)
    {
        $this->setBalance($this->getBalance() + $amount);

        return $this;
    }

    /**
     * @param $amount
     * @param Order $order
     * @param Quote $quote
     *
     * @return $this
     * @throws LocalizedException
     * @throws Exception
     */
    public function spentForOrder($amount, $order, $quote)
    {
        if (!$this->isActive($quote) || ($this->getBalance() < $amount)) {
            throw new LocalizedException(__('Gift Card balance is not enough'));
        }

        $this->setBalance($this->getBalance() - $amount)
            ->setAction(Action::ACTION_SPEND)
            ->setActionVars(['order_increment_id' => $order->getIncrementId(), 'auth' => $order->getCustomerName()])
            ->save();

        return $this;
    }

    /**
     * Update status for gift card
     *
     * @param $status
     *
     * @return $this
     * @throws Exception
     * @throws LocalizedException
     */
    public function updateStatus($status)
    {
        if (!in_array((int) $status, [Status::STATUS_ACTIVE, Status::STATUS_INACTIVE], true)) {
            throw new LocalizedException(__('Can only update status to "Active" or "Inactive"'));
        }

        if ($this->getStatus() > Status::STATUS_INACTIVE) {
            throw new LocalizedException(__('Cannot update status for gift code "%1"', $this->getCode()));
        }

        $this->setData('status', $status)->save();

        return $this;
    }

    /**
     * Update multiple status
     *
     * @param $ids
     * @param $status
     *
     * @return $this
     */
    public function updateStatuses($ids, $status)
    {
        if (!empty($ids)) {
            $this->getResource()->updateStatuses($ids, $status);
        }

        return $this;
    }

    /**
     * Get gift card status label
     *
     * @param null $status
     *
     * @return Phrase|string
     */
    public function getStatusLabel($status = null)
    {
        if ($status === null) {
            $status = $this->getStatus();
        }

        $allStatus = Status::getOptionArray();

        return isset($allStatus[$status]) ? $allStatus[$status] : __('Undefined');
    }

    /**
     * @param null $code
     *
     * @return null|string
     */
    public function getHiddenCode($code = null)
    {
        if ($code === null) {
            $code = $this->getCode();
        }

        if (!$this->_helper->getGeneralConfig('hidden/enable')) {
            return $code;
        }

        $codeLength = strlen($code);

        $numOfPrefix = (int) $this->_helper->getGeneralConfig('hidden/prefix');
        $numOfSuffix = (int) $this->_helper->getGeneralConfig('hidden/suffix');

        if ($codeLength - $numOfPrefix - $numOfSuffix <= 0) {
            return $code;
        }

        $hiddenChar = (string) $this->_helper->getGeneralConfig('hidden/character') ?: 'X';

        $prefix    = $numOfPrefix ? substr($code, 0, $numOfPrefix) : '';
        $suffix    = $numOfSuffix ? substr($code, -$numOfSuffix) : '';
        $character = str_repeat($hiddenChar, $codeLength - $numOfPrefix - $numOfSuffix);

        return $prefix . $character . $suffix;
    }

    /**
     * Is active gift card
     *
     * @param Quote|null $quote
     *
     * @return bool
     * @throws LocalizedException
     * @throws Exception
     */
    public function isActive($quote = null)
    {
        if ($this->_isActive !== null) {
            return $this->_isActive;
        }

        $this->_isActive = true;

        $storeId = $quote ? $quote->getStoreId() : $this->_helper->getStoreId();
        if (!$this->getId() || $storeId != $this->getStoreId()) {
            $this->_isActive = false;

            return $this->_isActive;
        }

        $this->processExpiredDate();
        $this->processStatus();

        $checkCond = true;
        if ($quote && $this->getConditionsSerialized()) {
            $rule = $this->ruleFactory->create();
            $rule->setConditionsSerialized($this->getConditionsSerialized());
            $address   = $this->getAddressToValidate($quote);
            $checkCond = $this->getConditionsSerialized() && $rule->getConditions()->validate($address);
        }

        if (!$checkCond || (int) $this->getStatus() !== Status::STATUS_ACTIVE) {
            $this->_isActive = false;
        } elseif ($poolId = $this->getPoolId()) {
            $pool            = $this->_poolFactory->create()->load($poolId);
            $this->_isActive = $pool->isActive();
        }

        return $this->_isActive;
    }

    /**
     * @param Quote $quote
     *
     * @return Address
     */
    protected function getAddressToValidate($quote)
    {
        if (!$this->addressToValidate) {
            $this->addressToValidate
                = clone($quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress());

            $this->addressToValidate->setData('total_qty', $quote->getItemsQty());
        }

        return $this->addressToValidate;
    }

    /**
     * Load Gift Card by code
     *
     * @param $code
     *
     * @return $this
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'code');
    }

    /**
     * @param $ids
     *
     * @return array|null
     */
    public function loadByIds($ids)
    {
        $collection = $this->getCollection()->addFieldToFilter('giftcard_id', $ids);
        if ($collection->getSize()) {
            return $collection->getData();
        }

        return null;
    }

    /**
     * Processing object after save data
     *
     * @return $this
     * @throws ConfigurationException
     * @throws Html2PdfException
     */
    public function afterSave()
    {
        parent::afterSave();

        if ($this->getAction()) {
            $oldData = $this->getOrigData();
            $newData = $this->getData();

            if ($oldData === null
                || !$this->compareData($oldData, $newData)
                || count(array_diff_assoc($oldData, $newData)) > 3
            ) {
                $this->_historyFactory->create()->setGiftCard($this)->save();

                if (!$this->getData('send_to_recipient') && !$this->isObjectNew()) {
                    $this->sendToRecipient(Email::EMAIL_TYPE_UPDATE);
                }
            }
        }

        if ($this->getData('send_to_recipient')) {
            $this->sendToRecipient(Email::EMAIL_TYPE_DELIVERY);
        }

        return $this;
    }

    /**
     * @param array $oldData
     * @param array $newData
     *
     * @return bool
     */
    public function compareData($oldData, $newData)
    {
        return strtotime($oldData[self::DELIVERY_DATE]) === strtotime($newData[self::DELIVERY_DATE])
            && strtotime($oldData[self::EXPIRED_AT]) === strtotime($newData[self::EXPIRED_AT])
            && (int) ($oldData[self::BALANCE]) === (int) ($newData[self::BALANCE]);
    }

    /**
     * Generate gift code
     *
     * @param null $pattern
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function generateCode($pattern = null)
    {
        if ($pattern === null) {
            $pattern = $this->getPattern() ?: $this->_helper->getCodePattern();
        }

        $pattern = strtoupper(str_replace(' ', '', $pattern));
        $code    = $pattern;

        $attempt = 10;
        do {
            if ($attempt-- <= 0) {
                throw new LocalizedException(__('Unable to generate gift code. Please check the setting and try again.'));
            }

            $patternString = '#\[([0-9]+)([AN]{1,2})\]#';
            if (preg_match($patternString, $pattern)) {
                $code = preg_replace_callback(
                    $patternString,
                    function ($param) {
                        $pool = (strpos($param[2], 'A')) === false ? '' : Random::CHARS_UPPERS;
                        $pool .= (strpos($param[2], 'N')) === false ? '' : Random::CHARS_DIGITS;

                        return $this->_mathRandom->getRandomString($param[1], $pool);
                    },
                    $pattern
                );
            }
        } while ($this->getResource()->checkCodeAvailable($this, $code));

        return $code;
    }

    /**
     * Process expired date
     *
     * @return $this
     * @throws Exception
     */
    protected function processExpiredDate()
    {
        $timezone      = $this->_helper->getGiftCardTimeZone($this);
        $baseExpiredAt = $this->getExpiredAt();

        if ($this->hasExpireAfter() && $this->getExpireAfter()) {
            $datetime     = new DateTime(null, $timezone);
            $expiredAfter = min($this->getExpireAfter(), 36500); // 100 years
            $datetime->add(new DateInterval("P{$expiredAfter}D"));
            $this->setExpiredAt($datetime->format('Y-m-d'));
        } elseif ($this->hasExpiredAt()) {
            $status = (int) $this->getStatus();

            if ($this->getExpiredAt()) {
                $expiredAt = new DateTime($this->dateFilter->filter($this->getExpiredAt()));
                $this->setExpiredAt($expiredAt->format('Y-m-d'));

                $expiredAtTimestamp = $expiredAt->setTime(23, 59);
                $nowDayTimestamp    = (new DateTime(null, $timezone));

                if ($status === Status::STATUS_ACTIVE && $expiredAtTimestamp < $nowDayTimestamp) {
                    $this->setStatus(Status::STATUS_EXPIRED);
                } elseif ($status === Status::STATUS_EXPIRED && $expiredAtTimestamp >= $nowDayTimestamp) {
                    $this->setStatus(Status::STATUS_ACTIVE);
                }
            } elseif ($status === Status::STATUS_EXPIRED) {
                $this->setStatus(Status::STATUS_ACTIVE);
            }
        }

        $this->setExpiredAt($baseExpiredAt);

        return $this;
    }

    /**
     * Change status depend on active balance
     *
     * @return $this
     */
    protected function processStatus()
    {
        if (!in_array(
            (int) $this->getStatus(),
            [Status::STATUS_PENDING, Status::STATUS_INACTIVE, Status::STATUS_EXPIRED, Status::STATUS_CANCELLED],
            true
        )) {
            if ($this->getBalance() > 0) {
                $this->setStatus(Status::STATUS_ACTIVE);
            } else {
                $this->setStatus(Status::STATUS_USED);
            }
        }

        return $this;
    }

    /**
     * @param null $giftCard
     * @param null $storeId
     *
     * @return bool
     * @throws Exception
     */
    public function canRedeem($giftCard = null, $storeId = null)
    {
        if ($giftCard === null) {
            $giftCard = $this;
        }

        $configRedeemable = $this->_helper->allowRedeemGiftCard($storeId);

        return $configRedeemable && $giftCard->isActive() && $giftCard->getCanRedeem();
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws LocalizedException
     */
    public function createMultiple($params)
    {
        $this->beforeSave();

        $giftCodes = $this->getResource()->createMultiple($this, $params);
        $giftCards = $this->getCollection()->addFieldToFilter('code', ['in' => $giftCodes]);
        if ($giftCards->getSize()) {
            $this->_historyFactory->create()
                ->getResource()
                ->createMultiple($giftCards, $this->getActionVars());
        }

        return $giftCodes;
    }

    /**
     * @param $data
     * @param $condition
     */
    public function updateMultiple($data, $condition)
    {
        $this->getResource()->updateMultiple($data, $condition);
        // todo update history for Gift Card if balance is changed
    }

    /**
     * Get Customer saved gift card
     *
     * @param $customerId
     *
     * @return array
     * @throws Exception
     */
    public function getGiftCardListForCustomer($customerId)
    {
        $giftCardList = [];

        /** @var ResourceModel\GiftCard\Collection $giftCards */
        $giftCards = $this->getCollection()
            ->addFieldToFilter('customer_ids', ['finset' => $customerId])
            ->setOrder('status', 'asc')
            ->setOrder('expired_at', 'desc');

        /** @var GiftCard $giftCard */
        foreach ($giftCards as $giftCard) {
            $historyData = [];
            /** @var Collection $histories */
            $histories = $this->_historyFactory->create()
                ->getCollection()
                ->addFieldToFilter('giftcard_id', $giftCard->getId())
                ->setOrder('created_at', 'desc');
            foreach ($histories as $history) {
                $history->addData([
                    'created_at_formatted' => $this->_helper->formatDate(
                        $history->getCreatedAt(),
                        IntlDateFormatter::MEDIUM
                    ),
                    'action_label'         => $history->getActionLabel(),
                    'amount_formatted'     => $this->_helper->convertPrice($history->getAmount()),
                    'status_label'         => $giftCard->getStatusLabel($history->getStatus()),
                    'action_detail'        => Action::getActionLabel($history->getAction(), $history->getExtraContent())
                ]);

                $historyData[] = $history->getData();
            }

            if ($poolId = $giftCard->getPoolId()) {
                $pool = $this->_poolFactory->create()->load($poolId);
                if ((int) $pool->getStatus() === GcStatus::STATUS_INACTIVE
                    && (int) $giftCard->getStatus() === Status::STATUS_ACTIVE) {
                    $giftCard->setStatus(Status::STATUS_INACTIVE);
                }
            }

            $giftCard->addData([
                'expired_at_formatted' => $giftCard->getExpiredAt()
                    ? $this->_helper->formatDate($giftCard->getExpiredAt(), IntlDateFormatter::MEDIUM)
                    : __('Permanent'),
                'status_label'         => $giftCard->getStatusLabel(),
                'balance_formatted'    => $this->_helper->convertPrice($giftCard->getBalance()),
                'can_redeem'           => $this->canRedeem($giftCard),
                'hidden_code'          => $giftCard->getHiddenCode(),
                'histories'            => $historyData
            ]);

            $giftCardList[] = $giftCard->getData();
        }

        return $giftCardList;
    }

    /**
     * Send Email to recipient
     *
     * @param $type
     * @param array $params
     *
     * @return $this
     * @throws Exception
     * @throws Html2PdfException
     * @throws ConfigurationException
     */
    public function sendToRecipient($type, $params = [])
    {
        switch ($this->getDeliveryMethod()) {
            case DeliveryMethods::METHOD_PRINT:
                $params['is_print'] = true;
            // Fall-through to send email
            case DeliveryMethods::METHOD_EMAIL:
                $this->_helper->getEmailHelper()->sendDeliveryEmail($this, $type, $params);
                break;
            case DeliveryMethods::METHOD_SMS:
                $this->_helper->getSmsHelper()->sendSms($this, $type);
                break;
            case DeliveryMethods::METHOD_POST:
                if ($type !== Email::EMAIL_TYPE_DELIVERY && $this->getIsSent()) {
                    $order = $this->_helper->getGiftCardOrder($this);
                    if ($order && $order->getId()) {
                        if ($order->getShippingAddress()) {
                            $this->setDeliveryAddress($order->getShippingAddress()->getEmail());
                        }
                        $this->_helper->getEmailHelper()->sendDeliveryEmail($this, $type, $params);
                    }
                }
                break;
        }

        if ($type === Email::EMAIL_TYPE_DELIVERY) {
            $this->_helper->getEmailHelper()->sendNoticeSenderEmail($this, Email::EMAIL_TYPE_NOTICE_SENDER);

            $this->_historyFactory->create()
                ->setGiftCard($this)
                ->setAction(Action::ACTION_SEND)
                ->save();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getGiftcardId()
    {
        return $this->getData(self::GIFTCARD_ID);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setGiftcardId($value)
    {
        return $this->setData(self::GIFTCARD_ID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritDoc}
     */
    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern()
    {
        return $this->getData(self::PATTERN);
    }

    /**
     * {@inheritDoc}
     */
    public function setPattern($value)
    {
        return $this->setData(self::PATTERN, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getInitBalance()
    {
        return $this->getData(self::INIT_BALANCE);
    }

    /**
     * {@inheritDoc}
     */
    public function setInitBalance($value)
    {
        return $this->setData(self::INIT_BALANCE, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * {@inheritDoc}
     */
    public function setBalance($value)
    {
        return $this->setData(self::BALANCE, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getCanRedeem()
    {
        return $this->getData(self::CAN_REDEEM);
    }

    /**
     * {@inheritDoc}
     */
    public function setCanRedeem($value)
    {
        return $this->setData(self::CAN_REDEEM, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getPoolId()
    {
        return $this->getData(self::POOL_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setPoolId($value)
    {
        return $this->setData(self::POOL_ID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setTemplateId($value)
    {
        return $this->setData(self::TEMPLATE_ID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritDoc}
     */
    public function setImage($value)
    {
        return $this->setData(self::IMAGE, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateFields()
    {
        return $this->getData(self::TEMPLATE_FIELDS);
    }

    /**
     * {@inheritDoc}
     */
    public function setTemplateFields($value)
    {
        return $this->setData(self::TEMPLATE_FIELDS, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerIds()
    {
        return $this->getData(self::CUSTOMER_IDS);
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomerIds($value)
    {
        return $this->setData(self::CUSTOMER_IDS, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderItemId()
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setOrderItemId($value)
    {
        return $this->setData(self::ORDER_ITEM_ID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderIncrementId()
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setOrderIncrementId($value)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getDeliveryMethod()
    {
        return $this->getData(self::DELIVERY_METHOD);
    }

    /**
     * {@inheritDoc}
     */
    public function setDeliveryMethod($value)
    {
        return $this->setData(self::DELIVERY_METHOD, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getDeliveryAddress()
    {
        return $this->getData(self::DELIVERY_ADDRESS);
    }

    /**
     * {@inheritDoc}
     */
    public function setDeliveryAddress($value)
    {
        return $this->setData(self::DELIVERY_ADDRESS, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getIsSent()
    {
        return $this->getData(self::IS_SENT);
    }

    /**
     * {@inheritDoc}
     */
    public function setIsSent($value)
    {
        return $this->setData(self::IS_SENT, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getDeliveryDate()
    {
        return $this->getData(self::DELIVERY_DATE);
    }

    /**
     * {@inheritDoc}
     */
    public function setDeliveryDate($value)
    {
        return $this->setData(self::DELIVERY_DATE, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getTimezone()
    {
        return $this->getData(self::TIMEZONE);
    }

    /**
     * {@inheritDoc}
     */
    public function setTimezone($value)
    {
        return $this->setData(self::TIMEZONE, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtraContent()
    {
        return $this->getData(self::EXTRA_CONTENT);
    }

    /**
     * {@inheritDoc}
     */
    public function setExtraContent($value)
    {
        return $this->setData(self::EXTRA_CONTENT, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setExpiredAt($value)
    {
        return $this->setData(self::EXPIRED_AT, $value);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }
}

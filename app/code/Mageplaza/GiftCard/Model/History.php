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

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Api\Data\GiftHistoryInterface;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\GiftCard\Action;

/**
 * Class History
 * @package Mageplaza\GiftCard\Model
 */
class History extends AbstractModel implements IdentityInterface, GiftHistoryInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'mageplaza_giftcard_history';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'giftcard_history';

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * Gift Card constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $helper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $helper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helper = $helper;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\History::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get history action label
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
     * @inheritdoc
     */
    public function beforeSave()
    {
        parent::beforeSave();

        /** @var GiftCard $giftCard */
        $giftCard   = $this->getGiftCard();
        $actionVars = $this->getActionVars() ?: $giftCard->getActionVars();

        $this->setData([
            'giftcard_id'   => $giftCard->getId(),
            'code'          => $giftCard->getCode(),
            'action'        => $this->getAction() ?: $giftCard->getAction(),
            'balance'       => $giftCard->getBalance(),
            'amount'        => $giftCard->getData('balance') - $giftCard->getOrigData('balance'),
            'status'        => $giftCard->getStatus(),
            'store_id'      => $giftCard->getStoreId(),
            'extra_content' => is_array($actionVars) ? DataHelper::jsonEncode($actionVars) : $actionVars
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHistoryId()
    {
        return $this->getData(self::HISTORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setHistoryId($value)
    {
        return $this->setData(self::HISTORY_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftcardId()
    {
        return $this->getData(self::GIFTCARD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftcardId($value)
    {
        return $this->setData(self::GIFTCARD_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($value)
    {
        return $this->setData(self::ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBalance($value)
    {
        return $this->setData(self::BALANCE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraContent()
    {
        return $this->getData(self::EXTRA_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtraContent($value)
    {
        return $this->setData(self::EXTRA_CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }
}

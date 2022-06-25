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
use Mageplaza\GiftCard\Api\Data\GiftPoolInterface;
use Mageplaza\GiftCard\Model\Source\Status;
use Zend_Db_Expr;

/**
 * Class Pool
 * @package Mageplaza\GiftCard\Model
 * @method getConditionsSerialized()
 */
class Pool extends AbstractModel implements IdentityInterface, GiftPoolInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'mageplaza_giftcard_pool';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_giftcard_pool';

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * Pool constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param GiftCardFactory $giftCardFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        GiftCardFactory $giftCardFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->giftCardFactory = $giftCardFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Pool::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        parent::beforeSave();

        if ($this->getId() && $this->getCanInherit()) {
            $dataUpdate    = [
                'can_redeem'            => $this->getCanRedeem(),
                'store_id'              => $this->getStoreId(),
                'template_id'           => $this->getTemplateId(),
                'image'                 => $this->getImage(),
                'expired_at'            => $this->getExpiredAt()
                    ? date('Y-m-d', strtotime($this->getExpiredAt()))
                    : null,
                'template_fields'       => $this->getTemplateFields(),
                'conditions_serialized' => $this->getConditionsSerialized(),
            ];
            $balanceChange = $this->getBalance() - $this->getOrigData('balance');
            if ($balanceChange !== 0) {
                $sqlVar                = '(`balance` + ' . $balanceChange . ')';
                $dataUpdate['balance'] = new Zend_Db_Expr("(CASE WHEN ({$sqlVar} < 0) THEN 0 ELSE {$sqlVar} END)");
            }

            $this->giftCardFactory->create()->updateMultiple($dataUpdate, ['pool_id = ?' => $this->getId()]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (int) $this->getStatus() === Status::STATUS_ACTIVE;
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

        return $allStatus[$status] ?? __('Undefined');
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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritDoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
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
    public function getCanInherit()
    {
        return $this->getData(self::CAN_INHERIT);
    }

    /**
     * {@inheritDoc}
     */
    public function setCanInherit($value)
    {
        return $this->setData(self::CAN_INHERIT, $value);
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
     * @return string
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
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }
}

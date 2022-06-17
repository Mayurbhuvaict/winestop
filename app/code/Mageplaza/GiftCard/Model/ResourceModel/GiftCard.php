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

namespace Mageplaza\GiftCard\Model\ResourceModel;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mageplaza\GiftCard\Helper\Data;

/**
 * Class GiftCard
 * @package Mageplaza\GiftCard\Model\ResourceModel
 */
class GiftCard extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('mageplaza_giftcard', 'giftcard_id');
    }

    /**
     * @param AbstractModel|\Mageplaza\GiftCard\Model\GiftCard $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $required = [
            'code'    => $object->getCode(),
            'balance' => $object->getBalance(),
        ];

        foreach ($required as $key => $value) {
            if ($value !== null && $value === '') {
                throw new LocalizedException(__('%1 value must not be empty', $key));
            }
        }

        parent::_beforeSave($object);

        $templateFields = $object->getTemplateFields();
        $extraContent   = $object->getExtraContent();

        if (is_array($templateFields)) {
            $object->setTemplateFields(Data::jsonEncode($templateFields));
        }

        if (is_object($templateFields)) {
            $object->setTemplateFields(Data::jsonEncode($templateFields->getData()));
        }

        if (is_array($extraContent)) {
            $object->setExtraContent(Data::jsonEncode($extraContent));
        }

        return $this;
    }

    /**
     * Check for unique values existence
     *
     * @param \Mageplaza\GiftCard\Model\GiftCard $object
     * @param $code
     *
     * @return bool
     * @throws LocalizedException
     */
    public function checkCodeAvailable($object, $code)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable());
        $select->where('code=?', trim($code));
        if ($object->getId() || (string) $object->getId() === '0') {
            $select->where($this->getIdFieldName() . '!=?', $object->getId());
        }
        $test = $this->getConnection()->fetchRow($select);

        return (boolean) $test;
    }

    /**
     * @param $ids
     * @param $status
     *
     * @return $this
     * @throws LocalizedException
     */
    public function updateStatuses($ids, $status)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            ['status' => $status],
            [$this->getIdFieldName() . ' IN (?)' => $ids]
        );

        return $this;
    }

    /**
     * Perform actions after object load
     *
     * @param AbstractModel|DataObject $object
     *
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);

        $templateFields = $object->getTemplateFields();
        $extraContent   = $object->getExtraContent();

        if ($templateFields) {
            $object->addData(Data::jsonDecode($templateFields));
        }

        if ($extraContent) {
            $object->setExtraContent(Data::jsonDecode($extraContent));
        }

        return $this;
    }

    /**
     * @param \Mageplaza\GiftCard\Model\GiftCard $giftCard
     * @param array $params
     *
     * @return array
     * @throws LocalizedException
     */
    public function createMultiple($giftCard, $params)
    {
        $resultData = [];
        $data       = [
            'init_balance'          => $giftCard->getBalance(),
            'balance'               => $giftCard->getBalance(),
            'status'                => $giftCard->getStatus(),
            'can_redeem'            => $giftCard->getCanRedeem(),
            'store_id'              => $giftCard->getStoreId(),
            'pool_id'               => $giftCard->getPoolId(),
            'template_id'           => $giftCard->getTemplateId(),
            'image'                 => $giftCard->getImage(),
            'template_fields'       => $giftCard->getTemplateFields(),
            'conditions_serialized' => $giftCard->getConditionsSerialized(),
            'expired_at'            => $giftCard->getExpiredAt()
        ];

        if (isset($params['qty'])) {
            $qty = $params['qty'];
            while ($qty--) {
                $data['code'] = $giftCard->generateCode($giftCard->getPattern());
                $resultData[] = $data;
            }
        }

        if (isset($params['codes'])) {
            foreach ($params['codes'] as $code) {
                $data['code'] = $code;
                $resultData[] = $data;
            }
        }

        $this->getConnection()->insertMultiple($this->getMainTable(), $resultData);

        return array_column($resultData, 'code');
    }

    /**
     * @param $data
     * @param $where
     *
     * @return $this
     * @throws LocalizedException
     */
    public function updateMultiple($data, $where)
    {
        $this->getConnection()->update($this->getMainTable(), $data, $where);

        return $this;
    }

    /**
     * Generate multi Gift Card transaction
     *
     * @param array $objects
     *
     * @return $this
     * @throws Exception
     */
    public function generateMultiGiftCard($objects)
    {
        $this->beginTransaction();

        try {
            /** @var \Mageplaza\GiftCard\Model\GiftCard $object */
            foreach ($objects as $object) {
                $object->save();
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}

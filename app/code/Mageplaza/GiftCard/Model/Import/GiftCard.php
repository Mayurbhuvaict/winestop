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

namespace Mageplaza\GiftCard\Model\Import;

use Exception;
use Magento\Backend\Model\Auth;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\Store\Model\StoreRepository;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCard as GiftCardModel;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\Import\GiftCard\RowValidatorInterface as ValidatorInterface;
use Mageplaza\GiftCard\Model\ResourceModel\Pool\CollectionFactory as PoolCollection;
use Mageplaza\GiftCard\Model\ResourceModel\Template\CollectionFactory as TemplateCollection;

/**
 * Class GiftCard
 * @package Mageplaza\GiftCard\Model\Import
 */
class GiftCard extends AbstractEntity
{
    /**
     * Columns
     */
    const COL_CODE        = 'code';
    const COL_BALANCE     = 'balance';
    const COL_STATUS      = 'status';
    const COL_CAN_REDEEM  = 'can_redeem';
    const COL_STORE_ID    = 'store_id';
    const COL_TEMPLATE_ID = 'template_id';
    const COL_POOL_ID     = 'pool_id';
    const COL_EXPIRED_AT  = 'expired_at';

    /** @inheritdoc */
    protected $masterAttributeCode = 'code';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates
        = [
            ValidatorInterface::ERROR_CODE_IS_EMPTY    => 'Code is empty',
            ValidatorInterface::ERROR_DUPLICATE_CODE   => 'Gift Code is duplicated',
            ValidatorInterface::ERROR_INVALID_POOL     => 'Pool in\'t exist',
            ValidatorInterface::ERROR_INVALID_TEMPLATE => 'Template in\'t exist',
            ValidatorInterface::ERROR_INVALID_WEBSITE  => 'Website in\'t exist',
            ValidatorInterface::ERROR_INVALID_STATUS   => 'Status not exist',
            ValidatorInterface::ERROR_INVALID_BALANCE  => 'Balance must be a number and greater than 0',
            ValidatorInterface::ERROR_INVALID_REDEEM   => 'Redeem must be 0 or 1'
        ];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [self::COL_CODE];

    /** @inheritdoc */
    protected $_availableBehaviors
        = [
            Import::BEHAVIOR_APPEND,
            Import::BEHAVIOR_REPLACE,
            Import::BEHAVIOR_DELETE
        ];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Valid column names
     *
     * @var array
     */
    protected $validColumnNames
        = [
            self::COL_CODE,
            self::COL_BALANCE,
            self::COL_CAN_REDEEM,
            self::COL_STATUS,
            self::COL_STORE_ID,
            self::COL_TEMPLATE_ID,
            self::COL_POOL_ID,
            self::COL_EXPIRED_AT
        ];

    /**
     * @var Auth
     */
    protected $_auth;

    /**
     * @var GiftCardModel
     */
    protected $giftCardModel;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var TemplateCollection
     */
    private $template;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * GiftCard constructor.
     *
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param ImportFactory $importFactory
     * @param Helper $resourceHelper
     * @param ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param Auth $auth
     * @param GiftCardModel $giftCardModel
     * @param DateTime $date
     * @param StoreRepository $storeRepository
     * @param PoolCollection $pool
     * @param TemplateCollection $template
     * @param array $data
     */
    public function __construct(
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        Helper $resourceHelper,
        ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        Auth $auth,
        GiftCardModel $giftCardModel,
        DateTime $date,
        StoreRepository $storeRepository,
        PoolCollection $pool,
        TemplateCollection $template,
        array $data = []
    ) {
        $this->_auth           = $auth;
        $this->giftCardModel   = $giftCardModel;
        $this->date            = $date;
        $this->storeRepository = $storeRepository;
        $this->pool            = $pool;
        $this->template        = $template;

        parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $data);
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'gift_card';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        $code = false;

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;
        // BEHAVIOR_DELETE use specific validation logic
        if (Import::BEHAVIOR_DELETE === $this->getBehavior()) {
            if (!isset($rowData[self::COL_CODE])) {
                $this->addRowError(ValidatorInterface::ERROR_CODE_IS_EMPTY, $rowNum);

                return false;
            }

            return true;
        }
        //Validate Code
        if (isset($rowData[self::COL_CODE])) {
            $code = $rowData[self::COL_CODE];
        }
        if ($code === false) {
            $this->addRowError(ValidatorInterface::ERROR_CODE_IS_EMPTY, $rowNum);
        }

        //Validate Status
        if (!in_array((int) $rowData[self::COL_STATUS], Status::getStatus(), true)) {
            $this->addRowError(ValidatorInterface::ERROR_INVALID_STATUS, $rowNum);
        }

        //Validate Balance
        if (!$rowData[self::COL_BALANCE]
            || !is_numeric($rowData[self::COL_BALANCE]) || $rowData[self::COL_BALANCE] < 0) {
            $this->addRowError(ValidatorInterface::ERROR_INVALID_BALANCE, $rowNum);
        }

        //Validate Redeem
        $canRedeem = (int) $rowData[self::COL_CAN_REDEEM];
        if ($canRedeem !== 0 && $canRedeem !== 1) {
            $this->addRowError(ValidatorInterface::ERROR_INVALID_REDEEM, $rowNum);
        }

        //Validate StoreId
        if (!$rowData[self::COL_STORE_ID] || !in_array($rowData[self::COL_STORE_ID], $this->getStoresId(), true)) {
            $this->addRowError(ValidatorInterface::ERROR_INVALID_WEBSITE, $rowNum);
        }

        //Validate PoolId
        if ($rowData[self::COL_POOL_ID]
            && !in_array($rowData[self::COL_POOL_ID], $this->pool->create()->getAllIds(), true)) {
            $this->addRowError(ValidatorInterface::ERROR_INVALID_POOL, $rowNum);
        }

        //Validate Template
        if ($rowData[self::COL_TEMPLATE_ID]
            && !in_array($rowData[self::COL_TEMPLATE_ID], $this->template->create()->getAllIds(), true)) {
            $this->addRowError(ValidatorInterface::ERROR_INVALID_TEMPLATE, $rowNum);
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create Gift card data from raw data.
     *
     * @return bool Result of operation.
     * @throws Exception
     */
    protected function _importData()
    {
        switch ($this->getBehavior()) {
            case Import::BEHAVIOR_DELETE:
                $this->deleteEntity();
                break;
            case Import::BEHAVIOR_REPLACE:
                $this->replaceEntity();
                break;
            case Import::BEHAVIOR_APPEND:
                $this->saveEntity();
                break;
            default:
                break;
        }

        return true;
    }

    /**
     * Save gift card code
     *
     * @return $this
     * @throws LocalizedException
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }

    /**
     * Replace newsletter subscriber
     *
     * @return $this
     * @throws LocalizedException
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();

        return $this;
    }

    /**
     * Deletes gift card from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
        $listCode = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);
                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowCode    = $rowData[self::COL_CODE];
                    $listCode[] = $rowCode;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }
        if ($listCode) {
            $this->deleteEntityFinish(array_unique($listCode));
        }

        return $this;
    }

    /**
     * Save and replace gift card
     * @return $this
     * @throws LocalizedException
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listCode = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_CODE_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowCode                = $rowData[self::COL_CODE];
                $listCode[]             = $rowCode;
                $entityList[$rowCode][] = [
                    self::COL_CODE        => $rowData[self::COL_CODE],
                    self::COL_BALANCE     => $rowData[self::COL_BALANCE],
                    self::COL_STATUS      => $rowData[self::COL_STATUS],
                    self::COL_CAN_REDEEM  => $rowData[self::COL_CAN_REDEEM],
                    self::COL_STORE_ID    => $rowData[self::COL_STORE_ID],
                    self::COL_TEMPLATE_ID => $rowData[self::COL_TEMPLATE_ID],
                    self::COL_POOL_ID     => $rowData[self::COL_POOL_ID],
                    self::COL_EXPIRED_AT  => $rowData[self::COL_EXPIRED_AT],
                    'extra_content'       => Data::jsonEncode(['auth' => $this->_auth->getUser()->getName()])
                ];
            }
            if ($behavior === Import::BEHAVIOR_REPLACE) {
                if ($listCode && $this->deleteEntityFinish(array_unique($listCode))) {
                    $this->saveEntityFinish($entityList);
                }
            } elseif ($behavior === Import::BEHAVIOR_APPEND) {
                $this->saveEntityFinish($entityList);
            }
        }

        return $this;
    }

    /**
     * @param array $entityData
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function saveEntityFinish(array $entityData)
    {
        $current = strtotime($this->date->date());

        if ($entityData) {
            $entityIn = [];
            foreach ($entityData as $entityRows) {
                foreach ($entityRows as $row) {
                    if ($row[self::COL_EXPIRED_AT]
                        && $current > strtotime($this->date->date('Y-m-d', $row[self::COL_EXPIRED_AT]))) {
                        $row[self::COL_STATUS] = Status::STATUS_EXPIRED;
                    }
                    $entityIn[] = $row;
                }
            }

            if ($entityIn) {
                $this->_connection->insertOnDuplicate($this->getGiftCardTable(), $entityIn, [
                    self::COL_CODE,
                    self::COL_BALANCE,
                    self::COL_STATUS,
                    self::COL_CAN_REDEEM,
                    self::COL_STORE_ID,
                    self::COL_TEMPLATE_ID,
                    self::COL_POOL_ID,
                    self::COL_EXPIRED_AT,
                    'extra_content'
                ]);
            }
        }

        return $this;
    }

    /**
     * @param array $listCode
     *
     * @return bool
     */
    protected function deleteEntityFinish(array $listCode)
    {
        if ($listCode) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->getGiftCardTable(),
                    $this->_connection->quoteInto('code IN (?)', $listCode)
                );

                return true;
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getGiftCardTable()
    {
        return $this->giftCardModel->getResource()->getMainTable();
    }

    /**
     * @return array
     */
    public function getStoresId()
    {
        $stores    = $this->storeRepository->getList();
        $storeList = [];
        foreach ($stores as $store) {
            if ($storeId = $store->getId()) {
                $storeList[] = $storeId;
            }
        }

        return $storeList;
    }
}

<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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

namespace Mageplaza\GiftCard\Model\Api;

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\GiftCard\Api\GiftHistoryManagementInterface;
use Mageplaza\GiftCard\Helper\Data as HelperData;
use Mageplaza\GiftCard\Model\HistoryFactory;

/**
 * Class GiftHistoryManagement
 * @package Mageplaza\GiftCard\Model\Api
 */
class GiftHistoryManagement implements GiftHistoryManagementInterface
{
    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * GiftCodeManagement constructor.
     *
     * @param HistoryFactory $historyFactory
     * @param HelperData $helperData
     */
    public function __construct(
        HistoryFactory $historyFactory,
        HelperData $helperData
    ) {
        $this->historyFactory = $historyFactory;
        $this->helperData     = $helperData;
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function get($id)
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('Module is disabled.'));
        }

        $collection = $this->historyFactory->create()->getCollection()->addFieldToFilter('giftcard_id', $id);

        return $collection->getItems();
    }
}

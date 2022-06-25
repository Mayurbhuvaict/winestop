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

namespace Mageplaza\GiftCard\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection;

/**
 * Class PoolAvailable
 * @package Mageplaza\GiftCard\Ui\Component\Listing\Columns
 */
class PoolAvailable extends Column
{
    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * PoolAvailable constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GiftCardFactory $giftCardFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GiftCardFactory $giftCardFactory,
        array $components = [],
        array $data = []
    ) {
        $this->giftCardFactory = $giftCardFactory;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $poolId = isset($item['pool_id']) ? $item['pool_id'] : null;

                /** @var Collection $collection */
                $collection = $this->giftCardFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('pool_id', $poolId);

                $totalSize = $collection->getSize();
                $collection->resetTotalRecords();

                $collection->addFieldToFilter('status', Status::STATUS_ACTIVE);
                $activeSize = $collection->getSize();

                $item[$this->getData('name')] = '<span style="font-weight: bold"><span style="color: forestgreen">' . $activeSize . '</span> / ' . $totalSize . '</span>';
            }
        }

        return $dataSource;
    }
}

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

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\GiftCard\Api\Data\GiftPoolInterface;
use Mageplaza\GiftCard\Api\Data\GiftPoolSearchResultInterfaceFactory;
use Mageplaza\GiftCard\Api\Data\TemplateFieldsInterfaceFactory;
use Mageplaza\GiftCard\Api\GiftPoolManagementInterface;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\ResourceModel\Pool\Collection;

/**
 * Class GiftPoolManagement
 * @package Mageplaza\GiftCard\Model\Api
 */
class GiftPoolManagement extends AbstractManagement implements GiftPoolManagementInterface
{
    /**
     * @var PoolFactory
     */
    private $poolFactory;

    /**
     * @var GiftCardFactory
     */
    private $giftCardFactory;

    /**
     * @var GiftPoolSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * GiftPoolManagement constructor.
     *
     * @param TemplateFieldsInterfaceFactory $templateFieldsFactory
     * @param GiftPoolSearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param PoolFactory $poolFactory
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(
        TemplateFieldsInterfaceFactory $templateFieldsFactory,
        CollectionProcessorInterface $collectionProcessor,
        GiftPoolSearchResultInterfaceFactory $searchResultFactory,
        PoolFactory $poolFactory,
        GiftCardFactory $giftCardFactory
    ) {
        $this->poolFactory         = $poolFactory;
        $this->giftCardFactory     = $giftCardFactory;
        $this->searchResultFactory = $searchResultFactory;
        parent::__construct($templateFieldsFactory, $collectionProcessor);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $searchResult */
        $searchResult = $this->searchResultFactory->create();

        return $this->getListEntity($searchResult, $searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->getEntity($this->poolFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->deleteEntity($this->poolFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     * @param GiftPoolInterface|Pool $entity
     */
    public function save(GiftPoolInterface $entity)
    {
        return $this->saveEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($id, $pattern, $qty)
    {
        $pool = $this->poolFactory->create()->load($id);

        if (!$pool->getId()) {
            throw new NoSuchEntityException(__("The entity that was requested doesn't exist. Verify the entity and try again."));
        }

        $giftCard = $this->giftCardFactory->create()
            ->setData($pool->getData())
            ->addData([
                'pattern'     => $pattern,
                'pool_id'     => $pool->getId(),
                'action_vars' => Data::jsonEncode(['pool_id' => $pool->getId()])
            ]);

        return $giftCard->createMultiple(['qty' => $qty]);
    }
}

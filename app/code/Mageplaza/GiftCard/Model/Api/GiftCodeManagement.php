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
use Mageplaza\GiftCard\Api\Data\GiftCodeInterface;
use Mageplaza\GiftCard\Api\Data\GiftCodeSearchResultInterfaceFactory;
use Mageplaza\GiftCard\Api\Data\TemplateFieldsInterfaceFactory;
use Mageplaza\GiftCard\Api\GiftCodeManagementInterface;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection;

/**
 * Class GiftCodeManagement
 * @package Mageplaza\GiftCard\Model\Api
 */
class GiftCodeManagement extends AbstractManagement implements GiftCodeManagementInterface
{
    /**
     * @var GiftCardFactory
     */
    private $giftCardFactory;

    /**
     * @var GiftCodeSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * GiftCodeManagement constructor.
     *
     * @param TemplateFieldsInterfaceFactory $templateFieldsFactory
     * @param GiftCodeSearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param GiftCardFactory $giftCardFactory
     */
    public function __construct(
        TemplateFieldsInterfaceFactory $templateFieldsFactory,
        CollectionProcessorInterface $collectionProcessor,
        GiftCodeSearchResultInterfaceFactory $searchResultFactory,
        GiftCardFactory $giftCardFactory
    ) {
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
        return $this->getEntity($this->giftCardFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->deleteEntity($this->giftCardFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     * @param GiftCodeInterface|GiftCard $entity
     */
    public function save(GiftCodeInterface $entity)
    {
        return $this->saveEntity($entity);
    }
}

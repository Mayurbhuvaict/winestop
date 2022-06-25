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

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\GiftCard\Api\Data\TemplateFieldsInterfaceFactory;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\Api\Data\TemplateFields;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\History;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection as GiftCardCollection;
use Mageplaza\GiftCard\Model\ResourceModel\Pool\Collection as PoolCollection;
use Mageplaza\GiftCard\Model\ResourceModel\Template\Collection as TemplateCollection;
use Mageplaza\GiftCard\Model\Template;

/**
 * Class AbstractManagement
 * @package Mageplaza\GiftCard\Model\Api
 */
class AbstractManagement
{
    /**
     * @var TemplateFieldsInterfaceFactory
     */
    private $templateFieldsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * GiftCodeManagement constructor.
     *
     * @param TemplateFieldsInterfaceFactory $templateFieldsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        TemplateFieldsInterfaceFactory $templateFieldsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->templateFieldsFactory = $templateFieldsFactory;
        $this->collectionProcessor   = $collectionProcessor;
    }

    /**
     * @param GiftCardCollection|PoolCollection|TemplateCollection $searchResult
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return GiftCardCollection|PoolCollection|TemplateCollection
     */
    public function getListEntity($searchResult, $searchCriteria)
    {
        $this->collectionProcessor->process($searchCriteria, $searchResult);
        $searchResult->setSearchCriteria($searchCriteria);
        /** @var GiftCard|Pool|Template $item */
        foreach ($searchResult->getItems() as $item) {
            $this->processData($item);
        }

        return $searchResult;
    }

    /**
     * @param GiftCard|Pool|Template|History $entity
     * @param int $id
     *
     * @return GiftCard|Pool|Template|History
     * @throws NoSuchEntityException
     */
    public function getEntity($entity, $id)
    {
        $entity->load($id);

        if (!$entity->getId()) {
            throw new NoSuchEntityException(__("The entity that was requested doesn't exist. Verify the entity and try again."));
        }

        $this->processData($entity);

        return $entity;
    }

    /**
     * @param GiftCard|Pool|Template $entity
     * @param int $id
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function deleteEntity($entity, $id)
    {
        $entity->load($id);

        if (!$entity->getId()) {
            throw new NoSuchEntityException(__("The entity that was requested doesn't exist. Verify the entity and try again."));
        }

        $entity->delete();

        return true;
    }

    /**
     * @param GiftCard|Pool|Template $entity
     *
     * @return GiftCard|Pool|Template
     * @throws Exception
     */
    public function saveEntity($entity)
    {
        $entity->save();

        return $this->get($entity->getId());
    }

    /**
     * @param GiftCard|Pool|Template $entity
     *
     * @return GiftCard|Pool|Template
     */
    protected function processData($entity)
    {
        /** @var TemplateFields $templateFields */
        $templateFields = $this->templateFieldsFactory->create();

        $templateFields->setData(Data::jsonDecode($entity->getTemplateFields()));

        $entity->setTemplateFields($templateFields);

        return $entity;
    }
}

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
use Mageplaza\GiftCard\Api\Data\GiftTemplateInterface;
use Mageplaza\GiftCard\Api\Data\GiftTemplateSearchResultInterfaceFactory;
use Mageplaza\GiftCard\Api\Data\TemplateFieldsInterfaceFactory;
use Mageplaza\GiftCard\Api\GiftTemplateManagementInterface;
use Mageplaza\GiftCard\Model\ResourceModel\Template\Collection;
use Mageplaza\GiftCard\Model\Template;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class GiftTemplateManagement
 * @package Mageplaza\GiftCard\Model\Api
 */
class GiftTemplateManagement extends AbstractManagement implements GiftTemplateManagementInterface
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var GiftTemplateSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * GiftTemplateManagement constructor.
     *
     * @param TemplateFieldsInterfaceFactory $templateFieldsFactory
     * @param GiftTemplateSearchResultInterfaceFactory $searchResultFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param TemplateFactory $templateFactory
     */
    public function __construct(
        TemplateFieldsInterfaceFactory $templateFieldsFactory,
        CollectionProcessorInterface $collectionProcessor,
        GiftTemplateSearchResultInterfaceFactory $searchResultFactory,
        TemplateFactory $templateFactory
    ) {
        $this->templateFactory     = $templateFactory;
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
        return $this->getEntity($this->templateFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->deleteEntity($this->templateFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     * @param GiftTemplateInterface|Template $entity
     */
    public function save(GiftTemplateInterface $entity)
    {
        return $this->saveEntity($entity);
    }

    /**
     * @param Template $entity
     *
     * @return Template
     */
    protected function processData($entity)
    {
        return $entity;
    }
}

<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Ui\DataProvider;

class FeedsProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory
     */
    protected $collection;
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * Class constructor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct($name, $primaryFieldName, $requestFieldName, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $collectionFactory, array $meta = [], array $data = [])
    {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}
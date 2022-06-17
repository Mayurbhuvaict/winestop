<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
























































































































































































































namespace Aheadworks\AdvancedReports\Model\Config\Backend;

use Aheadworks\AdvancedReports\Model\ResourceModel\CustomerSales\Range as CustomerSalesRangeResource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Model\Context as ModelContext;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Range
 *
 * @package Aheadworks\AdvancedReports\Model\Config\Backend
 */
class Range extends ConfigValue
{
    const CONFIG_RANGE_KEY = 'aw_arep_config_range';

    /**
     * @var CustomerSalesRangeResource
     */
    private $customerSalesRangeResource;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistor
     */
    private $dataPersistor;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param ModelContext $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param CustomerSalesRangeResource $customerSalesRangeResource
     * @param RequestInterface $request
     * @param DataPersistor $dataPersistor
     * @param JsonSerializer $serializer
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        ModelContext $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        CustomerSalesRangeResource $customerSalesRangeResource,
        RequestInterface $request,
        DataPersistor $dataPersistor,
        JsonSerializer $serializer,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->customerSalesRangeResource = $customerSalesRangeResource;
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $websiteId = (int) $this->request->getParam('website', 0);
        $savedValue = $this->dataPersistor->get(self::CONFIG_RANGE_KEY);
        if ($savedValue) {
            $value = $savedValue;
            $this->dataPersistor->clear(self::CONFIG_RANGE_KEY);
        } else {
            $value = $this->customerSalesRangeResource->loadConfigValue($websiteId);
        }
        $this->setValue($value);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = $this->prepareForSave($value);
        $this->setRangeValue($value);
        $this->setValue($this->serializer->serialize($value));
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        $websiteId = (int) $this->request->getParam('website', 0);
        $this->customerSalesRangeResource->saveConfigValue($this->getRangeValue(), $websiteId);
        $this->dataPersistor->clear(self::CONFIG_RANGE_KEY);
        return parent::afterSave();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $websiteId = (int) $this->request->getParam('website', 0);
        $this->customerSalesRangeResource->removeConfigValue($websiteId);
        return parent::afterDelete();
    }

    /**
     * Prepare config value for save
     *
     * @param array $value
     * @return array
     * @throws LocalizedException
     */
    private function prepareForSave($value)
    {
        unset($value['__empty']);
        $value = $this->removeDuplicates($value);
        $this->dataPersistor->set(self::CONFIG_RANGE_KEY, $value);
        $this->validate($value);

        return $value;
    }

    /**
     * Remove duplicates
     *
     * @param array $value
     * @return array
     */
    private function removeDuplicates($value)
    {
        $resultValue = [];
        foreach ($value as $valueRow) {
            if (!in_array($valueRow, $resultValue)) {
                $resultValue[] = $valueRow;
            }
        }
        return $resultValue;
    }

    /**
     * Validate config value before save
     *
     * @param array $value
     * @throws LocalizedException
     * @return $this
     */
    private function validate($value)
    {
        $this->validateFrom($value);
        $this->validateTo($value);
        $this->validateRanges($value);

        return $this;
    }

    /**
     * Validate Range From
     *
     * @param array $value
     * @throws LocalizedException
     * @return $this
     */
    private function validateFrom($value)
    {
        $zeroCount = 0;
        foreach ($value as $valueRow) {
            if ($valueRow['range_from'] == 0) {
                $zeroCount++;
            }
        }
        if ($zeroCount > 1) {
            throw new LocalizedException(__("Only one zero From value is possible"));
        }
        return $this;
    }

    /**
     * Validate Range To
     *
     * @param array $value
     * @throws LocalizedException
     * @return $this
     */
    private function validateTo($value)
    {
        $infinityCount = 0;
        foreach ($value as $valueRow) {
            if ($valueRow['range_to'] == '') {
                $infinityCount++;
            }
        }
        if ($infinityCount > 1) {
            throw new LocalizedException(__("Only one empty To value is possible"));
        }
        return $this;
    }

    /**
     * Validate Ranges
     *
     * @param array $value
     * @throws LocalizedException
     * @return $this
     */
    private function validateRanges($value)
    {
        foreach ($value as $valueRow) {
            if ($valueRow['range_to'] != '' && $valueRow['range_from'] >= $valueRow['range_to']) {
                throw new LocalizedException(__("From value should be less than To value"));
            }
        }

        $maxTo = 0;
        foreach ($value as $valueRow) {
            if ($valueRow['range_from'] >= $maxTo) {
                $maxTo = $valueRow['range_from'] + 1;
            }
            if ($valueRow['range_to'] != '' && $valueRow['range_to'] >= $maxTo) {
                $maxTo = $valueRow['range_to'] + 1;
            }
        }
        foreach ($value as $valueIndex => $valueRow) {
            if ($valueRow['range_to'] == '') {
                $valueRow['range_to'] = $maxTo;
            }
            foreach ($value as $checkIndex => $checkRow) {
                if ($checkRow['range_to'] == '') {
                    $checkRow['range_to'] = $maxTo;
                }
                if ($valueIndex != $checkIndex) {
                    if ((
                            $valueRow['range_from'] >= $checkRow['range_from']
                            && $valueRow['range_from'] <= $checkRow['range_to'])
                        || (
                            $valueRow['range_to'] >= $checkRow['range_from']
                            && $valueRow['range_to'] <= $checkRow['range_to']
                        )
                        || (
                            $valueRow['range_from'] >= $checkRow['range_from']
                            && $valueRow['range_to'] <= $checkRow['range_to']
                        )
                    ) {
                        throw new LocalizedException(__("Ranges can not be overlapped"));
                    }
                }
            }
        }
        return $this;
    }
}

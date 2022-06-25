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




























































































































































namespace Aheadworks\AdvancedReports\Model\Service;

use Aheadworks\AdvancedReports\Model\ResourceModel\ReportConfiguration as ReportConfigurationResource;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\ConfigInterfaceFactory;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\ConfigInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReportConfigurationService
 *
 * @package Aheadworks\AdvancedReports\Model\Service
 */
class ReportConfigurationService
{
    /**
     * @var ReportConfigurationResource
     */
    private $reportConfigurationResource;

    /**
     * @var ConfigInterfaceFactory
     */
    private $reportConfigurationFactory;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param ReportConfigurationResource $reportConfigurationResource
     * @param ConfigInterfaceFactory $reportConfigurationFactory
     * @param JsonSerializer $serializer
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ReportConfigurationResource $reportConfigurationResource,
        ConfigInterfaceFactory $reportConfigurationFactory,
        JsonSerializer $serializer,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->reportConfigurationResource = $reportConfigurationResource;
        $this->reportConfigurationFactory = $reportConfigurationFactory;
        $this->serializer = $serializer;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Get report configuration
     *
     * @param string $reportName
     * @return ConfigInterface|null
     * @throws LocalizedException
     */
    public function getReportConfiguration($reportName)
    {
        $serializedConfiguration = $this->reportConfigurationResource->loadConfiguration($reportName);
        $configuration = null;
        if ($serializedConfiguration) {
            $configuration = $this->reportConfigurationFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $configuration,
                $this->serializer->unserialize($serializedConfiguration),
                ConfigInterface::class
            );
        }

        return $configuration;
    }

    /**
     * Save report configuration
     *
     * @param string $reportName
     * @param ConfigInterface $reportConfiguration
     * @return bool
     * @throws \Exception
     */
    public function saveReportConfiguration($reportName, $reportConfiguration)
    {
        $configuration = $this->dataObjectProcessor->buildOutputDataArray(
            $reportConfiguration,
            ConfigInterface::class
        );
        $serializedConfiguration = $this->serializer->serialize($configuration);
        $this->reportConfigurationResource->saveConfiguration($reportName, $serializedConfiguration);

        return true;
    }
}

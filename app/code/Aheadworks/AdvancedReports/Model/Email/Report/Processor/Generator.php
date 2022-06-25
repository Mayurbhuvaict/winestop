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






















































namespace Aheadworks\AdvancedReports\Model\Email\Report\Processor;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator\Filter;
use Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator\FileCreator;
use Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator\FilenameCreator;
use Magento\Store\Model\Store;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Aheadworks\AdvancedReports\Model\Email\AttachmentInterface;
use Aheadworks\AdvancedReports\Model\Email\AttachmentInterfaceFactory;
use Aheadworks\AdvancedReports\Model\Email\Report\ConfigInterface;
use Magento\Framework\App\State;

/**
 * Class Generator
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Processor
 */
class Generator
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var FileCreator
     */
    private $fileCreator;

    /**
     * @var FilenameCreator
     */
    private $filenameCreator;

    /**
     * @var AppEmulation
     */
    private $appEmulation;

    /**
     * @var AttachmentInterfaceFactory
     */
    private $attachmentInterfaceFactory;

    /**
     * @var State
     */
    private $appState;

    /**
     * @param Filter $filter
     * @param FileCreator $fileCreator
     * @param AppEmulation $appEmulation
     * @param FilenameCreator $filenameCreator
     * @param AttachmentInterfaceFactory $attachmentInterfaceFactory
     * @param State $appState
     */
    public function __construct(
        Filter $filter,
        FileCreator $fileCreator,
        AppEmulation $appEmulation,
        FilenameCreator $filenameCreator,
        AttachmentInterfaceFactory $attachmentInterfaceFactory,
        State $appState
    ) {
        $this->filter = $filter;
        $this->fileCreator = $fileCreator;
        $this->appEmulation = $appEmulation;
        $this->filenameCreator = $filenameCreator;
        $this->attachmentInterfaceFactory = $attachmentInterfaceFactory;
        $this->appState = $appState;
    }

    /**
     * Generate reports as attachments
     *
     * @param ConfigInterface $reportConfig
     * @return AttachmentInterface[]
     * @throws \Exception
     */
    public function generate(ConfigInterface $reportConfig)
    {
        $reports = $reportConfig->getReportsToExport();
        $attachments = [];
        foreach ($reports as $report) {
            $this->filter->applyParams($report, $reportConfig);
            /** @var AttachmentInterface $attachment */
            $attachment = $this->attachmentInterfaceFactory->create();
            $attachment
                ->setAttachment($this->prepareFile($reportConfig))
                ->setFileName($this->prepareFileName($report, $reportConfig));
            $attachments[] = $attachment;
        }

        return $attachments;
    }

    /**
     * Prepare file
     *
     * @param ConfigInterface $reportConfig
     * @throws LocalizedException
     * @return string
     */
    private function prepareFile($reportConfig)
    {
        $this->appEmulation->startEnvironmentEmulation(Store::DEFAULT_STORE_ID, Area::AREA_ADMINHTML, true);
        $file = $this->fileCreator->create($reportConfig->getReportFormat());
        $this->appEmulation->stopEnvironmentEmulation();

        return $file;
    }

    /**
     * Prepare file name
     *
     * @param string $reportType
     * @param ConfigInterface $reportConfig
     * @return string
     * @throws \Exception
     */
    private function prepareFileName($reportType, $reportConfig)
    {
        return $this->appState->emulateAreaCode(
            Area::AREA_ADMINHTML,
            [$this->filenameCreator, 'create'],
            [$reportType, $reportConfig]
        );
    }
}

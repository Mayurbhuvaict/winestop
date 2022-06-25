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



















































namespace Aheadworks\AdvancedReports\Model\Email\Report;

use Aheadworks\AdvancedReports\Model\Config as ModuleConfig;
use Aheadworks\AdvancedReports\Model\Email\EmailMetadataInterface;
use Aheadworks\AdvancedReports\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReports\Model\Source\Email\Report\EmailVariables;
use Magento\Framework\App\Area;
use Magento\Store\Model\Store;
use Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator as ReportGenerator;
use Aheadworks\AdvancedReports\Model\Email\Report\Processor\Variable\Composite as VariableProcessorComposite;

/**
 * Class Processor
 *
 * @package Aheadworks\AdvancedReports\Model\Email\ScheduledReport
 */
class Processor
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ReportGenerator
     */
    private $reportGenerator;

    /**
     * @var EmailMetadataInterfaceFactory
     */
    private $emailMetadataFactory;

    /**
     * @var VariableProcessorComposite
     */
    private $variableProcessorComposite;

    /**
     * @param ModuleConfig $moduleConfig
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param ReportGenerator $reportGenerator
     * @param VariableProcessorComposite $variableProcessorComposite
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        ReportGenerator $reportGenerator,
        VariableProcessorComposite $variableProcessorComposite
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->emailMetadataFactory = $emailMetadataFactory;
        $this->reportGenerator = $reportGenerator;
        $this->variableProcessorComposite = $variableProcessorComposite;
    }

    /**
     * Preparing email data to be sent
     *
     * @param ConfigInterface $config
     * @return EmailMetadataInterface[]|bool
     * @throws \Exception
     */
    public function process(ConfigInterface $config)
    {
        $attachments = $this->reportGenerator->generate($config);
        $emailMetaDataList = [];
        foreach ($config->getRecipients() as $recipientEmail) {
            /** @var EmailMetadataInterface $emailMetaData */
            $emailMetaData = $this->emailMetadataFactory->create();
            $emailMetaData
                ->setTemplateId($this->getTemplateId())
                ->setTemplateOptions($this->getTemplateOptions())
                ->setTemplateVariables($this->prepareTemplateVariables($config))
                ->setSenderName($this->getSenderName())
                ->setSenderEmail($this->getSenderEmail())
                ->setRecipientEmail($recipientEmail)
                ->setAttachments($attachments);

            $emailMetaDataList[] = $emailMetaData;
        }

        return $emailMetaDataList;
    }

    /**
     * Retrieve template id
     *
     * @return string
     */
    private function getTemplateId()
    {
        return $this->moduleConfig->getScheduledEmailTemplate();
    }

    /**
     * Retrieve sender name
     *
     * @return string
     */
    private function getSenderName()
    {
        return $this->moduleConfig->getSenderName();
    }

    /**
     * Retrieve sender email
     *
     * @return string
     */
    private function getSenderEmail()
    {
        return $this->moduleConfig->getSenderEmail();
    }

    /**
     * Prepare template options
     *
     * @return array
     */
    private function getTemplateOptions()
    {
        return [
            'area' => Area::AREA_ADMINHTML,
            'store' => Store::DEFAULT_STORE_ID
        ];
    }

    /**
     * Prepare template variables
     *
     * @param ConfigInterface $reportConfig
     * @return array
     */
    private function prepareTemplateVariables(ConfigInterface $reportConfig)
    {
        $templateVariables = [
            EmailVariables::REPORT_CONFIG => $reportConfig
        ];

        return $this->variableProcessorComposite->prepareVariables($templateVariables);
    }
}

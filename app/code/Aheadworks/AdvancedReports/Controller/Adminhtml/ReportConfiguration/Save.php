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


























namespace Aheadworks\AdvancedReports\Controller\Adminhtml\ReportConfiguration;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\AdvancedReports\Model\Service\ReportConfigurationService;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\ConfigInterface;
use Aheadworks\AdvancedReports\Model\ReportConfiguration\ConfigInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Save
 *
 * @package Aheadworks\AdvancedReports\Controller\Adminhtml\ReportConfiguration
 */
class Save extends BackendAction
{
    /**
     * @var ReportConfigurationService
     */
    private $reportConfigurationService;

    /**
     * @var ConfigInterfaceFactory
     */
    private $reportConfigurationFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param ReportConfigurationService $reportConfigurationService
     * @param ConfigInterfaceFactory $reportConfigurationFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        ReportConfigurationService $reportConfigurationService,
        ConfigInterfaceFactory $reportConfigurationFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context);
        $this->reportConfigurationService = $reportConfigurationService;
        $this->reportConfigurationFactory = $reportConfigurationFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Check email action
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $reportConfiguration = $this->getRequest()->getParam('report_configuration');
        $reportName = $this->getRequest()->getParam('report_name');
        if (!$reportConfiguration || !$reportName) {
            $result = [
                'error' => __('Provided data is not correct'),
            ];
            return $resultJson->setData($result);
        }

        $configuration = $this->reportConfigurationFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $configuration,
            $reportConfiguration,
            ConfigInterface::class
        );

        try {
            $this->reportConfigurationService->saveReportConfiguration($reportName, $configuration);
            $result = [
                'result' => __('OK'),
            ];
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }

        return $resultJson->setData($result);
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $reportName = $this->getRequest()->getParam('report_name');
        return $this->_authorization->isAllowed('Aheadworks_AdvancedReports::reports_' . $reportName);
    }
}

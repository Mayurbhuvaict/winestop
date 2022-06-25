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

use Aheadworks\AdvancedReports\Model\Email\Report\ConfigProvider\GlobalConfig as ReportGlobalConfig;
use Aheadworks\AdvancedReports\Model\Email\Report\ConfigProvider\Validator as ConfigValidator;

/**
 * Class ConfigProvider
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report
 */
class ConfigProvider
{
    /**
     * @var ReportGlobalConfig
     */
    private $reportGlobalConfig;

    /**
     * @var ConfigValidator
     */
    private $validator;

    /**
     * @param ReportGlobalConfig $reportGlobalConfig
     * @param ConfigValidator $validator
     */
    public function __construct(
        ReportGlobalConfig $reportGlobalConfig,
        ConfigValidator $validator
    ) {
        $this->reportGlobalConfig = $reportGlobalConfig;
        $this->validator = $validator;
    }

    /**
     * Prepare report config
     *
     * @return ConfigInterface|bool
     */
    public function getConfig()
    {
        $globalConfig = $this->reportGlobalConfig->getConfig();
        if (!$this->validator->isValid($globalConfig)) {
            return false;
        }

        return $globalConfig;
    }
}

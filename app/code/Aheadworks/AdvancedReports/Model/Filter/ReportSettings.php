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





























































































































































































namespace Aheadworks\AdvancedReports\Model\Filter;

use Magento\Framework\App\RequestInterface;

/**
 * Class ReportSettings
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class ReportSettings implements FilterInterface
{
    /**
     * @var string
     */
    const REPORT_SETTINGS_PARAM = 'report_settings';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array|null
     */
    private $reportSettings;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (null === $this->reportSettings) {
            $this->reportSettings = [];

            $reportSettingParams = $this->request->getParam(self::REPORT_SETTINGS_PARAM);
            if (empty($reportSettingParams) || !is_array($reportSettingParams)) {
                return $this->reportSettings;
            }

            foreach ($reportSettingParams as $name => $value) {
                $this->reportSettings[$name] = $value;
            }
        }

        return $this->reportSettings;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return [];
    }

    /**
     * Retrieve report setting param
     *
     * @param string $param
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getReportSettingParam($param, $default = null)
    {
        $reportSettings = $this->getValue();
        if (isset($reportSettings[$param])) {
            return $reportSettings[$param];
        }

        return $default;
    }
}

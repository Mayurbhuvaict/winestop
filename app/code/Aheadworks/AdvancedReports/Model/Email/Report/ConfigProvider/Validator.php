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





















































namespace Aheadworks\AdvancedReports\Model\Email\Report\ConfigProvider;

use Aheadworks\AdvancedReports\Model\Email\Report\ConfigInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\ConfigProvider
 */
class Validator
{
    /**
     * Check if config is valid
     *
     * @param ConfigInterface $reportConfig
     * @return bool
     */
    public function isValid(ConfigInterface $reportConfig)
    {
        $result = true;

        if (!is_array($reportConfig->getReportsToExport()) || empty($reportConfig->getReportsToExport())) {
            $result = false;
        }
        if (!is_array($reportConfig->getRecipients()) || empty($reportConfig->getRecipients())) {
            $result = false;
        }
        if (empty($reportConfig->getWhenToSendFrequency())) {
            $result = false;
        }

        return $result;
    }
}

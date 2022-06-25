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






































































































































































namespace Aheadworks\AdvancedReports\Model\Source\Email\Report;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\AdvancedReports\Model\Config\Backend\ReportListProvider;

/**
 * Class Type
 *
 * @package Aheadworks\AdvancedReports\Model\Source\Email\Report
 */
class Type implements OptionSourceInterface
{
    /**
     * @var ReportListProvider
     */
    private $reportListProvider;

    /**
     * @var array
     */
    private $options;

    /**
     * @param ReportListProvider $reportListProvider
     */
    public function __construct(
        ReportListProvider $reportListProvider
    ) {
        $this->reportListProvider = $reportListProvider;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->reportListProvider->getReportListAsOptions();
        }

        return $this->options;
    }

    /**
     * Get report label for report type
     *
     * @param string $reportType
     * @return string
     */
    public function getReportLabel($reportType)
    {
        $reportLabel = '';
        $options = $this->toOptionArray();
        foreach ($options as $option) {
            if ($option['value'] == $reportType) {
                $reportLabel = $option['label'];
                break;
            }
        }

        return $reportLabel;
    }
}

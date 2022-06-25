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









































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Dashboard\Widgets;

/**
 * Class NumericWidget
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Dashboard\Widgets
 */
class NumericWidget extends AbstractWidget
{
    /**
     * {@inheritdoc}
     */
    protected function getMetricOptions()
    {
        return array_merge(
            $this->getSalesReportMetrics(),
            $this->getTrafficAndConversionsReportMetrics(),
            $this->getAbandonedCartsReportMetrics()
        );
    }
}

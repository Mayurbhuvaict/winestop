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































































































































































































































namespace Aheadworks\AdvancedReports\Cron;

use Aheadworks\AdvancedReports\Model\DatesGroupingManagement;

/**
 * Class UpdatePeriodTables
 *
 * @package Aheadworks\AdvancedReports\Cron
 */
class UpdatePeriodTables
{
    /**
     * @var DatesGroupingManagement
     */
    private $datesGroupingManagement;

    /**
     * @param DatesGroupingManagement $datesGroupingManagement
     */
    public function __construct(
        DatesGroupingManagement $datesGroupingManagement
    ) {
        $this->datesGroupingManagement = $datesGroupingManagement;
    }

    /**
     * Update dates grouping tables
     *
     * @return $this
     */
    public function execute()
    {
        $this->datesGroupingManagement->updateTables();
        return $this;
    }
}

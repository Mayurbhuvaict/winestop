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






























namespace Aheadworks\AdvancedReports\Model;

use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

/**
 * Class DatesGroupingManagement
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class DatesGroupingManagement
{
    /**
     * @var DatesGroupingFactory
     */
    private $datesGroupingFactory;

    /**
     * @param DatesGroupingFactory $datesGroupingFactory
     */
    public function __construct(
        DatesGroupingFactory $datesGroupingFactory
    ) {
        $this->datesGroupingFactory = $datesGroupingFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTables()
    {
        $updateTableKeys = [
            DatesGrouping\Day::KEY,
            DatesGrouping\Week::KEY,
            DatesGrouping\Month::KEY,
            DatesGrouping\Quarter::KEY,
            DatesGrouping\Year::KEY
        ];
        foreach ($updateTableKeys as $updateTableKey) {
            $this->datesGroupingFactory->create($updateTableKey)->updateTable();
        }
    }
}

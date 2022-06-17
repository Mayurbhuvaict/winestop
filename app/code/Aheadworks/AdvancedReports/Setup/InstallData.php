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




























































































































































































































namespace Aheadworks\AdvancedReports\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\AdvancedReports\Model\DatesGroupingManagement;

/**
 * Class InstallData
 *
 * @package Aheadworks\AdvancedReports\Setup
 */
class InstallData implements InstallDataInterface
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
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->datesGroupingManagement->updateTables();
    }
}

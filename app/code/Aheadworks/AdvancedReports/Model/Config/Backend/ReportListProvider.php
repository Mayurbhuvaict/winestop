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

























































































































































































































namespace Aheadworks\AdvancedReports\Model\Config\Backend;

use Magento\Backend\Model\Menu as BackendMenu;
use Magento\Backend\Model\Menu\Config as BackendMenuConfig;

/**
 * Class ReportListProvider
 *
 * @package Aheadworks\AdvancedReports\Model\Config\Backend
 */
class ReportListProvider
{
    /**
     * Root menu item ID of Advanced Reports
     */
    const ROOT_MENU_ITEM_ID = 'Aheadworks_AdvancedReports::main';

    /**
     * Menu item id substring to remove when preparing option value
     */
    const MENU_ITEM_ID_PART_TO_REMOVE = 'Aheadworks_AdvancedReports::reports_';

    /**
     * @var array
     */
    private $menuItemsIdToExclude = [
        'Aheadworks_AdvancedReports::reports_dashboard'
    ];

    /**
     * @var BackendMenuConfig
     */
    private $backendMenuConfig;

    /**
     * @param BackendMenuConfig $backendMenuConfig
     */
    public function __construct(
        BackendMenuConfig $backendMenuConfig
    ) {
        $this->backendMenuConfig = $backendMenuConfig;
    }

    /**
     * Get report list as options
     *
     * @return array
     * @throws \Exception
     */
    public function getReportListAsOptions()
    {
        $options = [];

        $reportMenuItems = $this->getReportListAsMenuItems();
        foreach ($reportMenuItems as $reportMenuItem) {
            if (in_array($reportMenuItem->getId(), $this->menuItemsIdToExclude)) {
                continue;
            }
            $options[] = [
                'value' => str_replace(
                    self::MENU_ITEM_ID_PART_TO_REMOVE,
                    '',
                    $reportMenuItem->getId()
                ),
                'label' => $reportMenuItem->getTitle()
            ];
        }
        return $options;
    }

    /**
     * Get report list as backend menu items
     *
     * @throws \Exception
     * @return BackendMenu
     */
    private function getReportListAsMenuItems()
    {
        $backendMenu = $this->backendMenuConfig->getMenu();
        $rootMenuItem = $backendMenu->get(self::ROOT_MENU_ITEM_ID);
        return $rootMenuItem->getChildren();
    }
}

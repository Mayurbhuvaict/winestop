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







































































































































































































namespace Aheadworks\AdvancedReports\Model\Toolbar;

use Aheadworks\AdvancedReports\Model\Toolbar\Menu\ItemInterface;
use Aheadworks\AdvancedReports\Model\Toolbar\Menu\ItemFactory;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class MenuPool
 *
 * @package Aheadworks\AdvancedReports\Model\Toolbar
 */
class MenuPool
{
    /**
     * @var array
     */
    private $menuItems = [];

    /**
     * @var array
     */
    private $menuItemsInstances = [];

    /**
     * @var ItemFactory
     */
    private $menuItemFactory;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param ItemFactory $menuItemFactory
     * @param AuthorizationInterface $authorization
     * @param array $menuItems
     */
    public function __construct(
        ItemFactory $menuItemFactory,
        AuthorizationInterface $authorization,
        array $menuItems = []
    ) {
        $this->menuItemFactory = $menuItemFactory;
        $this->authorization = $authorization;
        $this->menuItems = $menuItems;
    }

    /**
     * Retrieve menu items
     *
     * @return ItemInterface[]
     * @throws NotFoundException
     */
    public function getMenuItems()
    {
        $menuItemsInstances = [];
        foreach ($this->menuItems as $menuItemKey => $menuItemData) {
            $menuItem = $this->getMenuItem($menuItemKey);
            if ($menuItem->getResource() && !$this->authorization->isAllowed($menuItem->getResource())) {
                continue;
            }

            $menuItemsInstances[] = $menuItem;
        }

        return $menuItemsInstances;
    }

    /**
     * Get menuItems instance
     *
     * @param string $menuItem
     * @return ItemInterface
     * @throws NotFoundException
     */
    public function getMenuItem($menuItem)
    {
        if (!isset($this->menuItemsInstances[$menuItem])) {
            if (!isset($this->menuItems[$menuItem])) {
                throw new NotFoundException(__('Unknown menu item: %s requested', $menuItem));
            }
            $menuItemInstance = $this->menuItemFactory->create(['data' => $this->menuItems[$menuItem]]);
            if (!$menuItemInstance instanceof ItemInterface) {
                throw new NotFoundException(
                    __('Configuration instance %s does not implement required interface.', $menuItem)
                );
            }
            $this->menuItemsInstances[$menuItem] = $menuItemInstance;
        }
        return $this->menuItemsInstances[$menuItem];
    }
}

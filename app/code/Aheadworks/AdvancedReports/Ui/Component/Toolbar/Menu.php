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













































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Toolbar;

use Aheadworks\AdvancedReports\Model\Toolbar\Menu\Item\Modifier as MenuItemModifier;
use Aheadworks\AdvancedReports\Model\Toolbar\MenuPool;
use Aheadworks\AdvancedReports\Ui\Component\OptionsContainer;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Store
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Toolbar
 */
class Menu extends OptionsContainer
{
    /**
     * @var MenuPool
     */
    private $menuPool;

    /**
     * @var MenuItemModifier
     */
    private $menuItemModifier;

    /**
     * @param ContextInterface $context
     * @param MenuPool $menuPool
     * @param MenuItemModifier $menuItemModifier
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        MenuPool $menuPool,
        MenuItemModifier $menuItemModifier,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->menuPool = $menuPool;
        $this->menuItemModifier = $menuItemModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->prepareConfig();

        parent::prepare();
    }

    /**
     * Prepare config
     *
     * @return $this
     */
    private function prepareConfig()
    {
        $options = $this->getMenuOptions();
        $config = $this->getData('config');
        $config['options'] = $options;
        $config['currentValue'] = $this->getCurrentValue($options);
        $this->setData('config', $config);

        return $this;
    }

    /**
     * Retrieve menu options
     *
     * @return array
     */
    private function getMenuOptions()
    {
        $options = [];
        try {
            foreach ($this->menuPool->getMenuItems() as $menuItem) {
                $options[] = $this->menuItemModifier->modify($menuItem);
            }
        } catch (NotFoundException $e) {
            $options = [];
        }

        return $options;
    }

    /**
     * Retrieve current value
     *
     * @param array $options
     * @return null|string
     */
    private function getCurrentValue($options)
    {
        foreach ($options as $option) {
            if ($option['isCurrent']) {
                return $option['value'];
            }
        }

        return null;
    }
}

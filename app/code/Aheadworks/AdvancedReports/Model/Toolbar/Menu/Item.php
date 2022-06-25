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








































































































































































































namespace Aheadworks\AdvancedReports\Model\Toolbar\Menu;

use Magento\Framework\DataObject;

/**
 * Class Item
 *
 * @package Aheadworks\AdvancedReports\Model\Toolbar\Menu
 */
class Item extends DataObject implements ItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->getData(self::RESOURCE);
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return $this->getData(self::CONTROLLER);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkAttributes()
    {
        return $this->getData(self::LINK_ATTRIBUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalClasses()
    {
        return $this->getData(self::ADDITIONAL_CLASSES);
    }
}

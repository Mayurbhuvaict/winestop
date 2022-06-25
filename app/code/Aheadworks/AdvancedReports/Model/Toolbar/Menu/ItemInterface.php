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

/**
 * Interface ItemInterface
 *
 * @package Aheadworks\AdvancedReports\Model\Toolbar\Menu
 */
interface ItemInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const PATH = 'path';
    const LABEL = 'label';
    const RESOURCE = 'resource';
    const CONTROLLER = 'controller';
    const LINK_ATTRIBUTES = 'link_attributes';
    const ADDITIONAL_CLASSES = 'additional_classes';
    /**#@-*/

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get resource
     *
     * @return string
     */
    public function getResource();

    /**
     * Get controller
     *
     * @return string
     */
    public function getController();

    /**
     * Get link attributes
     *
     * @return string[]
     */
    public function getLinkAttributes();

    /**
     * Get additional classes
     *
     * @return string[]
     */
    public function getAdditionalClasses();
}

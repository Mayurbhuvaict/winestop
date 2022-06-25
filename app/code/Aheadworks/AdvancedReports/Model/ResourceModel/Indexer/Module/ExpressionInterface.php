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







































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module;

/**
 * Interface ExpressionInterface
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module
 */
interface ExpressionInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const MODULE_NAME   = 'module_name';
    const VALUE         = 'value';
    /**#@-*/

    /**
     * Get module name
     *
     * @return string
     */
    public function getModuleName();

    /**
     * Set module name
     *
     * @param string $name
     * @return $this
     */
    public function setModuleName($name);

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);
}

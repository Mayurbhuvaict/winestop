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


























































































































































































namespace Aheadworks\AdvancedReports\Model\Filter;

/**
 * Interface FilterInterface
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
interface FilterInterface
{
    /**
     * Retrieve filter value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Retrieve default filter value
     *
     * @return mixed
     */
    public function getDefaultValue();
}

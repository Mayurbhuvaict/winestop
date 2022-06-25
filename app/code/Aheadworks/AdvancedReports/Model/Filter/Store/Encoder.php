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






































































































































































































namespace Aheadworks\AdvancedReports\Model\Filter\Store;

/**
 * Class Encoder
 *
 * @package Aheadworks\AdvancedReports\Model\Filter\Store
 */
class Encoder
{
    /**
     * @var string
     */
    const DELIMITER = '_';

    /**
     * Encode scope values
     *
     * @param string $scope
     * @param int $value
     * @return string
     */
    public function encode($scope, $value)
    {
        return implode(self::DELIMITER, [$scope, $value]);
    }

    /**
     * Decode scope values
     *
     * @param string $params
     * @return array
     */
    public function decode($params)
    {
        return explode(self::DELIMITER, $params);
    }
}

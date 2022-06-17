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
















































































































































































































namespace Aheadworks\AdvancedReports\Model\Url;

/**
 * Class Base64Coder
 * @package Aheadworks\AdvancedReports\Model\Url
 */
class Base64Coder
{
    /**
     * Decode base64 fragment
     *
     * @param string $encoded
     * @return string
     * phpcs:disable Magento2.Functions
     */
    public static function decode($encoded)
    {
        return base64_decode($encoded);
    }

    /**
     * Encode base64 fragment
     *
     * @param string $fragment
     * @return string
     * phpcs:disable Magento2.Functions
     */
    public static function encode($fragment)
    {
        return base64_encode($fragment);
    }
}

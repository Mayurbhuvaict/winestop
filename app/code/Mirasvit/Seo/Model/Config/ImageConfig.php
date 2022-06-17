<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as ScopeInterface;

class ImageConfig
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * ImageConfig constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isFriendlyUrlEnabled()
    {
        if (isset($this->cache['isFriendlyUrlEnabled'])) {
            return $this->cache['isFriendlyUrlEnabled'];
        }
        $res = $this->scopeConfig->getValue(
            'seo/image/is_enable_image_friendly_url',
            ScopeInterface::SCOPE_STORE
        );
        $this->cache['isFriendlyUrlEnabled'] = $res;
        return $res;
    }

    /**
     * @return bool
     */
    public function isFriendlyAltEnabled()
    {
        if (isset($this->cache['isFriendlyAltEnabled'])) {
            return $this->cache['isFriendlyAltEnabled'];
        }
        $res = $this->scopeConfig->getValue(
            'seo/image/is_enable_image_alt',
            ScopeInterface::SCOPE_STORE
        );
        $this->cache['isFriendlyAltEnabled'] = $res;
        return $res;
    }

    /**
     * @return string
     */
    public function getUrlTemplate()
    {
        $imageUrlTemplate = $this->scopeConfig->getValue(
            'seo/image/image_url_template',
            ScopeInterface::SCOPE_STORE
        );

        if (!$imageUrlTemplate) {
            $imageUrlTemplate = '[product_name]';
        }

        return $imageUrlTemplate;
    }


    /**
     * @return string
     */
    public function getAltTemplate()
    {
        return $this->scopeConfig->getValue(
            'seo/image/image_alt_template',
            ScopeInterface::SCOPE_STORE
        );
    }
}

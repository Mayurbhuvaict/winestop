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


declare(strict_types=1);

namespace Mirasvit\SeoToolbar\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;

class Config
{
    private $scopeConfig;

    private $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request     = $request;
    }

    public function isToolbarAllowed(): bool
    {
        if ($this->request->getParam('debug') && $this->request->getParam('debug') == 'seo') {
            return true;
        }

        $isEnabled = $this->scopeConfig->getValue(
            'seo/seo_toolbar/is_active'
        );

        if (!$isEnabled) {
            return false;
        }

        $ips = $this->scopeConfig->getValue('seo/seo_toolbar/allowed_ip');

        if ($ips == '') {
            return true;
        }

        $ips = explode(',', $ips);
        $ips = array_map('trim', $ips);

        $keys = ['REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_CF_CONNECTING_IP'];

        foreach ($keys as $key) {
            if (isset($_SERVER[$key]) && in_array($_SERVER[$key], $ips)) {
                return true;
            }
        }

        return false;
    }
}

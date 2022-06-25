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


namespace Mirasvit\SeoAudit\Model;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\SeoAudit\Service\ServerLoadService;

class ConfigProvider
{
    private $scopeConfig;

    private $serverLoadService;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ServerLoadService $serverLoadService
    ) {
        $this->scopeConfig       = $scopeConfig;
        $this->serverLoadService = $serverLoadService;
    }

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo_audit/general/is_enabled');
    }

    public function getServerLoadThreshold(): int
    {
        return (int)$this->scopeConfig->getValue('seo_audit/general/server_load_threshold');
    }

    public function shouldRunAudit(): bool
    {
        return $this->isEnabled() && $this->getServerLoadRate() <= $this->getServerLoadThreshold();
    }

    public function getServerLoadRate(): int
    {
        return $this->serverLoadService->getRate();
    }
}

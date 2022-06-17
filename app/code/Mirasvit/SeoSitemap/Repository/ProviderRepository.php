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



namespace Mirasvit\SeoSitemap\Repository;

use Magento\Framework\Module\Manager as ModuleManager;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;

class ProviderRepository
{
    /**
     * @var ProviderInterface[]
     */
    private $providerPool;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * ProviderRepository constructor.
     * @param ModuleManager $moduleManager
     * @param array $pool
     */
    public function __construct(
        ModuleManager $moduleManager,
        array $pool = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->providerPool  = $pool;
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders()
    {
        $providers = [];
        foreach ($this->providerPool as $provider) {
            if ($this->isApplicable($provider)) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    /**
     * @param int $storeId
     * @return \Generator
     */
    public function initSitemapItems($storeId)
    {
        foreach ($this->getProviders() as $provider) {
            $result = $provider->initSitemapItem($storeId);

            if (!empty($result)) {
                yield $result;
            }
        }
    }

    /**
     * @param ProviderInterface $provider
     * @return bool
     */
    private function isApplicable(ProviderInterface $provider)
    {
        $module = $provider->getModuleName();

        if (!$this->moduleManager->isEnabled($module)) {
            return false;
        }

        return $provider->isApplicable();
    }
}

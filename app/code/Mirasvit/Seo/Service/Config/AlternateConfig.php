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

namespace Mirasvit\Seo\Service\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Core\Service\SerializeService;

class AlternateConfig implements \Mirasvit\Seo\Api\Config\AlternateConfigInterface
{
    protected $scopeConfig;

    protected $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getAlternateHreflang(int $storeId): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/general/is_alternate_hreflang',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return array|string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getAlternateManualConfig(int $storeId, bool $hreflang = false)
    {
        $config = $this->getPreparedAlternateManualConfig();

        if (!is_array($config)) {
            return [];
        }
        $result = [];
        $storeGroup = false;
        $storeHreflangResult = false;

        foreach ($config as $value) {
            if ($value['option'] == $storeId) {
                $storeGroup = $value['pattern'];
                $storeHreflangResult = $value['hreflang'];
            }
            $result[$value['pattern']][] = $value['option'];
        }

        if ($hreflang) {
            return $storeHreflangResult;
        }

        return ($storeGroup
            && isset($result[$storeGroup])
            && in_array($storeId, $result[$storeGroup])) ? $result[$storeGroup] : [];
    }

    protected function getPreparedAlternateManualConfig(): array
    {
        $config = (string)$this->scopeConfig->getValue(
            'seo/general/alternate_configurable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $configDecode = json_decode($config);

        if (is_object($configDecode)) {
            $config = (array)$configDecode;
            foreach ($config as $key => $value) {
                if (is_object($value)) {
                    $config[$key] = (array)$value;
                }
            }
        }

        if (!is_array($config) && $config != '[]') {
            $srcConfig = $config;
            $config    = SerializeService::decode($config);
            if (!$config) {
                $config = [0 => $srcConfig];
            }
        }

        return $config;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAlternateManualXDefault(array $storeUrls): ?string
    {
        $xDefaultUrl = null;
        $config = $this->scopeConfig->getValue('seo/general/configurable_hreflang_x_default');
        if ($config == '[]' || !$config) {
            $config = [];
        } elseif ($decode = json_decode($config)) {
            $config = [];
            if (is_object($decode)) {
                $decode = (array)$decode;
                foreach ($decode as $key => $value) {
                    if (is_object($value)) {
                        $config[$key] = (array)$value;
                    }
                }
            }
        } else {
            $srcConfig = $config;
            $config    = SerializeService::decode($config);
            if (!$config) {
                $config = [0 => $srcConfig];
            }
        }
        $storeIds = array_keys($storeUrls);
        foreach ($config as $value) {
            if (in_array($value['option'], $storeIds)) {
                $xDefaultUrl = $storeUrls[$value['option']];
                break;
            }
        }

        return $xDefaultUrl;
    }

    public function isHreflangLocaleCodeAddAutomatical(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_hreflang_locale_code_automatical');
    }

    public function isHreflangCutCategoryAdditionalData(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo/general/is_hreflang_cut_category_additional_data');
    }

    public function getXDefault(): string
    {
        return (string)$this->scopeConfig->getValue(
            'seo/general/is_hreflang_x_default',
            ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getStore()->getWebsiteId()
        );
    }

    public function getHreflangLocaleCode(int $storeId): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/general/hreflang_locale_code',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }
}

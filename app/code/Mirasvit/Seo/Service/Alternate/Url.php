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

namespace Mirasvit\Seo\Service\Alternate;

use Magento\Store\Api\Data\StoreInterface;
use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;

class Url implements \Mirasvit\Seo\Api\Service\Alternate\UrlInterface
{
    protected $context;

    protected $alternateConfig;

    protected $seoData;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $stores = [];

    /**
     * @var array
     */
    protected $storesBaseUrlsCountValues = [];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig,
        \Mirasvit\Seo\Helper\Data $seoData
    ) {
        $this->context         = $context;
        $this->alternateConfig = $alternateConfig;
        $this->seoData         = $seoData;
        $this->storeManager    = $this->context->getStoreManager();
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getStoresCurrentUrl(): array
    {
        $alternateManualConfig = false;
        $currentStoreGroup = $this->storeManager->getStore()->getGroupId();
        $currentStore = $this->storeManager->getStore();
        $alternateAddMethod = $this->alternateConfig->getAlternateHreflang((int)$currentStore->getId());
        if ($alternateAddMethod == AlternateConfig::ALTERNATE_CONFIGURABLE) {
            $alternateManualConfig = $this->alternateConfig->getAlternateManualConfig((int)$currentStore->getId());
        }
        $storesNumberInGroup = 0;
        $storeUrls = [];
        $storesBaseUrls = [];

        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getIsActive()
                && ((!$alternateManualConfig
                    && $store->getGroupId() == $currentStoreGroup
                    && $this->alternateConfig->getAlternateHreflang((int)$store->getId()))
                || ($alternateManualConfig
                    && in_array($store->getId(), $alternateManualConfig)))
            ) {
                //we works only with stores which have the same store group
                $this->stores[$store->getId()] = $store;
                $currentUrl = $store->getCurrentUrl(false);
                $storesBaseUrls[$store->getId()] = $store->getBaseUrl();
                $storeUrls[$store->getId()] = new \Magento\Framework\DataObject(
                    [
                        'store_base_url' => $store->getBaseUrl(),
                        'current_url' => $currentUrl,
                        'store_code' => $store->getCode()
                    ]
                );

                ++$storesNumberInGroup;
            }
        }

        $isSimilarLinks = (count($storesBaseUrls) - count(array_unique($storesBaseUrls)) > 0) ? true : false;

        if (count($storeUrls) > 1) {
            foreach ($storeUrls as $storeId => $storeData) {
                $storeUrls[$storeId] = $this->_storeUrlPrepare(
                    $storesBaseUrls,
                    $storeData->getStoreBaseUrl(),
                    $storeData->getCurrentUrl(),
                    $storeData->getStoreCode(),
                    $isSimilarLinks,
                    $alternateAddMethod
                );
            }
        }

        $this->storesBaseUrlsCountValues = array_count_values($storesBaseUrls);
        //array with quantity of identical Base Urls

        if ($storesNumberInGroup > 1 && count($storeUrls) > 1) { //if a current store is multilanguage
            return $storeUrls;
        }

        return [];
    }

    public function getStores(): array
    {
        return $this->stores;
    }
    
    public function getUrlAddition(StoreInterface $store): string
    {
        $urlAddition = (isset($this->storesBaseUrlsCountValues[$store->getBaseUrl()])
            && $this->storesBaseUrlsCountValues[$store->getBaseUrl()] > 1) ?
            strstr(htmlspecialchars_decode($store->getCurrentUrl(false)), '?') : '';

        return $urlAddition;
    }

    /**
     * Prepare store current url.
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _storeUrlPrepare(
        array $storesBaseUrls,
        string $storeBaseUrl,
        string $currentUrl,
        string $storeCode,
        bool $isSimilarLinks,
        int $alternateAddMethod
    ): string {
        if (strpos($currentUrl, $storeBaseUrl) === false && !$alternateAddMethod) {
            $currentUrl = str_replace($storesBaseUrls, $storeBaseUrl, $currentUrl); // fix bug with incorrect base urls
        }

        $currentUrl = str_replace('&amp;', '&', $currentUrl);
        $currentUrl = preg_replace('/SID=(.*?)(&|$)/', '', $currentUrl);

        //cut get params for AMASTY_XLANDING if "Cut category additional data for alternate url" enabled
        if ($this->alternateConfig->isHreflangCutCategoryAdditionalData()
            && $this->seoData->getFullActionCode() == AlternateConfig::AMASTY_XLANDING) {
            $currentUrl = strtok($currentUrl, '?');
        }

        $deleteStoreQuery = (substr_count($storeBaseUrl, '/') > 3) ? true : false;

        if (strpos($currentUrl, '___store=' . $storeCode) === false
            || (!$deleteStoreQuery && $isSimilarLinks)) {
            return $currentUrl;
        }

        if (strpos($currentUrl, '?___store=' . $storeCode) !== false
            && strpos($currentUrl, '&') === false) {
            $currentUrl = str_replace('?___store=' . $storeCode, '', $currentUrl);
        } elseif (strpos($currentUrl, '?___store=' . $storeCode) !== false
            && strpos($currentUrl, '&') !== false) {
            $currentUrl = str_replace('?___store=' . $storeCode . '&', '?', $currentUrl);
        } elseif (strpos($currentUrl, '&___store=' . $storeCode) !== false) {
            $currentUrl = str_replace('&___store=' . $storeCode, '', $currentUrl);
        }

        return $currentUrl;
    }
}

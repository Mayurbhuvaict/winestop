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

namespace Mirasvit\SeoAutolink\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getTarget(): array
    {
        return explode(',', (string)$this->scopeConfig->getValue('seoautolink/autolink/target'));
    }

    public function isAllowedTarget(string $target): bool
    {
        return in_array($target, $this->getTarget());
    }

    public function getExcludedTags(int $storeId = null): array
    {
        $conf = (string)$this->scopeConfig->getValue(
            'seoautolink/autolink/excluded_tags',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );

        $tags = explode("\n", trim($conf));
        $tags = array_map('trim', $tags);
        $tags = array_diff($tags, [0, null]);

        return $tags;
    }

    public function getSkipLinks(int $storeId = null): array
    {
        $conf = (string)$this->scopeConfig->getValue(
            'seoautolink/autolink/skip_links_for_page',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );

        $links = explode("\n", trim($conf));
        $links = array_map('trim', $links);
        $links = array_diff($links, [0, null]);

        return $links;
    }

    public function getLinksLimitPerPage(int $storeId = null): ?int
    {
        $linksLimit = $this->scopeConfig->getValue(
            'seoautolink/autolink/links_limit_per_page',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );

        if (empty($linksLimit) || (int)$linksLimit == 0) {
            return null;
        }

        return (int)$linksLimit;
    }
}

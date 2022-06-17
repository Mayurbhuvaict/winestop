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

namespace Mirasvit\SeoContent\Service\Content\Modifier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Model\Config;

class PrefixSuffixModifier implements ModifierInterface
{
    private $config;

    private $scopeConfig;

    public function __construct(
        Config $config,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->config      = $config;
        $this->scopeConfig = $scopeConfig;
    }

    public function modify(ContentInterface $content, ?string $forceApplyTo = null): ContentInterface
    {
        $metaTitle = $content->getMetaTitle();

        if (!$metaTitle) {
            return $content;
        }

        if (!$this->config->addPrefixSuffixToMetaTitle()) {
            return $content;
        }

        $prefix = $this->getPrefix();

        if ($prefix && strpos($metaTitle, $prefix) !== 0) {
            $metaTitle = $prefix . ' ' . $metaTitle;
        }

        $suffix = $this->getSuffix();

        if ($suffix && strpos($metaTitle, $suffix) !== strlen($metaTitle) - strlen($suffix)) {
            $metaTitle = $metaTitle . ' ' . $suffix;
        }

        $content->setMetaTitle($metaTitle);

        return $content;
    }

    private function getPrefix(): ?string
    {
        return $this->scopeConfig->getValue(
            'design/head/title_prefix',
            ScopeInterface::SCOPE_STORE
        );
    }

    private function getSuffix(): ?string
    {
        return $this->scopeConfig->getValue(
            'design/head/title_suffix',
            ScopeInterface::SCOPE_STORE
        );
    }
}

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

namespace Mirasvit\SeoAutolink\Service\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Autolink implements \Mirasvit\SeoAutolink\Api\Config\AutolinkInterface
{
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getTemplates(int $store = null): array
    {
        $conf = $this->scopeConfig->getValue(
            'seoautolink/autolink/add_links_inside_templates',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );

        $template = explode("\n", trim((string)$conf));
        $template = array_map('trim', $template);
        $template = array_diff($template, [0, null]);

        return $template;
    }
}


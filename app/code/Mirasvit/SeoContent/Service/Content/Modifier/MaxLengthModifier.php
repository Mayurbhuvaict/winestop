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

use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Model\Config;

class MaxLengthModifier implements ModifierInterface
{
    private $config;

    private $stateService;

    private $storeManager;

    public function __construct(
        Config $config,
        StateServiceInterface $stateService,
        StoreManagerInterface $storeManager
    ) {
        $this->config       = $config;
        $this->stateService = $stateService;
        $this->storeManager = $storeManager;
    }

    public function modify(ContentInterface $content, ?string $forceApplyTo = null): ContentInterface
    {
        $store = $this->storeManager->getStore();

        if ($length = $this->config->getMetaTitleLength($store)) {
            $content->setMetaTitle($this->truncate($content->getMetaTitle(), $length));
        }

        if ($length = $this->config->getMetaDescriptionLength($store)) {
            $content->setMetaDescription($this->truncate($content->getMetaDescription(), $length));
        }

        if ($this->stateService->isProductPage() || $forceApplyTo == 'product') {
            if ($length = $this->config->getProductNameLength($store)) {
                $content->setTitle($this->truncate($content->getTitle(), $length));
            }

            if ($length = $this->config->getProductShortDescriptionLength($store)) {
                $content->setShortDescription($this->truncate($content->getShortDescription(), $length));
            }
        }

        return $content;
    }

    private function truncate(string $string, int $limit): string
    {
        return mb_substr($string, 0, $limit);
    }
}

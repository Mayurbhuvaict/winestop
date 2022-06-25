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

namespace Mirasvit\SeoAutolink\Plugin;

use Magento\Framework\View\TemplateEngineFactory;
use Magento\Framework\View\TemplateEngineInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoAutolink\Api\Config\AutolinkInterface;
use Mirasvit\SeoAutolink\Service\AddLinks\AddLinksFactory;
use Mirasvit\SeoAutolink\Service\TextProcessorService;

class AddLinkInsideTemplates
{
    private $autolinkConfig;

    private $addLinks;

    private $storeManager;

    private $textProcessorService;

    public function __construct(
        AutolinkInterface $autolinkConfig,
        AddLinksFactory $addLinks,
        StoreManagerInterface $storeManager,
        TextProcessorService $textProcessorService
    ) {
        $this->autolinkConfig       = $autolinkConfig;
        $this->addLinks             = $addLinks;
        $this->storeManager         = $storeManager;
        $this->textProcessorService = $textProcessorService;
    }

    /**
     * Add autolinks in templates depending of the SEOAutolink configuration
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(
        TemplateEngineFactory $subject,
        TemplateEngineInterface $invocationResult
    ): TemplateEngineInterface {
        $store     = $this->storeManager->getStore();
        $templates = $this->autolinkConfig->getTemplates((int)$store->getId());

        if ($templates) {
            $res = $this->addLinks->create([
                'subject'       => $invocationResult,
                'templates'     => $templates,
                'replaceHelper' => $this->textProcessorService,
            ]);

            return $res;
        }

        return $invocationResult;
    }
}

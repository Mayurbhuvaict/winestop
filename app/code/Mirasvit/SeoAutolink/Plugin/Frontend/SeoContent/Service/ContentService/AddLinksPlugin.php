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



namespace Mirasvit\SeoAutolink\Plugin\Frontend\SeoContent\Service\ContentService;

use Mirasvit\SeoAutolink\Model\Config;
use Mirasvit\SeoAutolink\Model\Config\Source\Target;
use Mirasvit\SeoAutolink\Service\TextProcessorService;

/**
 * Purpose: Add links to SEO description
 */
class AddLinksPlugin
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var TextProcessorService
     */
    private $textProcessorService;

    /**
     * AddLinksPlugin constructor.
     * @param Config $config
     * @param TextProcessorService $textProcessorService
     */
    public function __construct(
        Config $config,
        TextProcessorService $textProcessorService
    ) {
        $this->config               = $config;
        $this->textProcessorService = $textProcessorService;
    }

    /**
     * @param \Mirasvit\SeoContent\Service\ContentService    $subject
     * @param \Mirasvit\SeoContent\Api\Data\ContentInterface $content
     *
     * @return \Mirasvit\SeoContent\Api\Data\ContentInterface
     */
    public function afterProcessCurrentContent($subject, $content)
    {
        $description = $content->getDescription();

        if ($this->config->isAllowedTarget(Target::SEO_DESCRIPTION)) {
            $description = $this->textProcessorService->addLinks($description);
            $content->setDescription($description);
        }

        return $content;
    }
}

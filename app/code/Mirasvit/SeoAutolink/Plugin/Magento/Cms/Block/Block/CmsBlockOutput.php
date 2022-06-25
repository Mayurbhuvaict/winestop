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



namespace Mirasvit\SeoAutolink\Plugin\Magento\Cms\Block\Block;

use Mirasvit\SeoAutolink\Model\Config;
use Mirasvit\SeoAutolink\Model\Config\Source\Target;
use Mirasvit\SeoAutolink\Service\TextProcessorService;

class CmsBlockOutput
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TextProcessorService
     */
    protected $textProcessorService;

    /**
     * CmsBlockOutput constructor.
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
     * @param \Magento\Cms\Model\Page $subject
     * @param string $result
     *
     * @return string
     */
    public function afterToHtml($subject, $result)
    {
        if ($this->config->isAllowedTarget(Target::CMS_BLOCK)) {
            $result = $this->textProcessorService->addLinks($result);
        }

        return $result;
    }
}

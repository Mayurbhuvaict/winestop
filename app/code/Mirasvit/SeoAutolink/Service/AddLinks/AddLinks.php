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



namespace Mirasvit\SeoAutolink\Service\AddLinks;

use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\TemplateEngineInterface;
use Mirasvit\SeoAutolink\Service\TextProcessorService;

class AddLinks implements \Mirasvit\SeoAutolink\Api\Service\AddLinks\AddLinksInterface, TemplateEngineInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $subject;

    /**
     * @var array
     */
    private $templates;

    /**
     * @var TextProcessorService
     */
    private $replaceHelper;


    /**
     * @param TemplateEngineInterface $subject
     * @param array                   $templates
     * @param mixed                   $replaceHelper
     */
    public function __construct(TemplateEngineInterface $subject, $templates, $replaceHelper)
    {
        $this->subject       = $subject;
        $this->templates     = $templates;
        $this->replaceHelper = $replaceHelper;
    }

    /**
     * Insert autolinks into the rendered block contents
     * {@inheritdoc}
     */
    public function render(BlockInterface $block, $templateFile, array $dictionary = [])
    {
        $result = $this->subject->render($block, $templateFile, $dictionary);

        $isTemplateUsed = array_filter(
            $this->templates,
            function ($el) use ($templateFile) {
                return strpos($templateFile, $el) !== false;
            }
        );

        if ($isTemplateUsed) {
            $result = $this->addLinks($result);
        }

        return $result;
    }

    /**
     * @param string $result
     *
     * @return string
     */
    public function addLinks($result)
    {
        if (substr_count($result, '</') < self::MAX_TAGS_NUMBER) {
            return $this->replaceHelper->addLinks($result);
        }

        return $result;
    }
}

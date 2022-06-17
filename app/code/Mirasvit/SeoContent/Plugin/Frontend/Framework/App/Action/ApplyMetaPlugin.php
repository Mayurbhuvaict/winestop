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



namespace Mirasvit\SeoContent\Plugin\Frontend\Framework\App\Action;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Mirasvit\SeoContent\Service\ContentService;

class ApplyMetaPlugin
{
    private $contentService;

    private $pageConfig;

    private $layout;

    private $request;
    
    public function __construct(
        HttpRequest $request,
        ContentService $contentService,
        PageConfig $pageConfig,
        LayoutInterface $layout
    ) {
        $this->contentService = $contentService;
        $this->pageConfig     = $pageConfig;
        $this->layout         = $layout;
        $this->request        = $request;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param object                                 $response
     *
     * @return object
     */
    public function afterDispatch($subject, $response)
    {
        if ($this->request->isAjax() || $subject instanceof \Magento\Framework\App\Action\Forward) {
            return $response;
        }
        if (!$this->contentService->isProcessablePage()) {
            return $response;
        }

        $content = $this->contentService->getCurrentContent();

        if ($content->getTitle()) {
            $this->pageConfig->getTitle()->set($content->getTitle());
        }

        # 2.3+
        if ($content->getMetaTitle() && method_exists($this->pageConfig, 'setMetaTitle')) {
            try {
                $this->pageConfig->setMetaTitle($content->getMetaTitle());
            } catch (\Exception $e) {
            }
        }

        if ($content->getMetaDescription()) {
            $this->pageConfig->setDescription($content->getMetaDescription());
        }

        if ($content->getMetaKeywords()) {
            $this->pageConfig->setKeywords($content->getMetaKeywords());
        }

        if ($content->getMetaRobots()) {
            $this->pageConfig->setRobots($content->getMetaRobots());
        }

        if ($content->getTitle()) {
            /** @var \Magento\Theme\Block\Html\Title $titleBlock */
            $titleBlock = $this->layout->getBlock('page.main.title');

            if ($titleBlock) {
                $titleBlock->setPageTitle($content->getTitle());
            }
        }

        return $response;
    }
}

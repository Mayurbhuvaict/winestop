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
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Service\ContentService;
use Mirasvit\SeoContent\Service\RewriteService;

class PutDefaultMetaPlugin
{
    private $contentService;

    private $rewriteService;

    private $pageConfig;

    private $layout;
    
    private $request;

    public function __construct(
        HttpRequest $request,
        ContentService $contentService,
        RewriteService $rewriteService,
        PageConfig $pageConfig,
        LayoutInterface $layout
    ) {
        $this->contentService = $contentService;
        $this->rewriteService = $rewriteService;
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

        $meta                                     = [];
        $meta[ContentInterface::META_TITLE]       = $this->pageConfig->getTitle()->get();
        $meta[ContentInterface::META_KEYWORDS]    = $this->pageConfig->getKeywords();
        $meta[ContentInterface::META_DESCRIPTION] = $this->pageConfig->getDescription();

        /** @var \Magento\Theme\Block\Html\Title $titleBlock */
        $titleBlock = $this->layout->getBlock('page.main.title');

        if ($titleBlock) {
            $meta[ContentInterface::TITLE] = $titleBlock->getPageTitle();
        }

        $this->contentService->putDefaultMeta($meta);

        return $response;
    }
}

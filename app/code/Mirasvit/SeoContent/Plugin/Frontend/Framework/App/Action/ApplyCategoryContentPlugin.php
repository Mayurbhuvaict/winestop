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
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoContent\Service\ContentService;
use Mirasvit\SeoContent\Service\StateService;

class ApplyCategoryContentPlugin
{
    private $contentService;

    private $stateService;
    
    private $request;

    public function __construct(
        HttpRequest $request,
        ContentService $contentService,
        StateServiceInterface $stateService
    ) {
        $this->contentService = $contentService;
        $this->stateService   = $stateService;
        $this->request        = $request;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param object                                 $response
     * @return object
     */
    public function afterDispatch($subject, $response)
    {
        if ($this->request->isAjax() || $subject instanceof \Magento\Framework\App\Action\Forward) {
            return $response;
        }

        if (!$this->stateService->isCategoryPage()) {
            return $response;
        }

        $content = $this->contentService->getCurrentContent();

        if ($content->getCategoryDescription()) {
            $this->stateService->getCategory()->setData('description', $content->getCategoryDescription());
        }

        return $response;
    }
}

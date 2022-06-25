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



namespace Mirasvit\SeoMarkup\Plugin\Frontend\Framework\Controller\Result;

use Magento\Framework\App\ResponseInterface;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;
use Mirasvit\SeoMarkup\Model\Config\PageConfig;

class RemoveNativeRsPlugin
{
    /**
     * @var StateServiceInterface
     */
    private $stateService;

    /**
     * @var ProductConfig
     */
    private $productConfig;

    /**
     * @var CategoryConfig
     */
    private $categoryConfig;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * RemoveNativeRsPlugin constructor.
     * @param StateServiceInterface $stateService
     * @param ProductConfig $productConfig
     * @param CategoryConfig $categoryConfig
     * @param PageConfig $pageConfig
     * @param ResponseInterface $response
     */
    public function __construct(
        StateServiceInterface $stateService,
        ProductConfig $productConfig,
        CategoryConfig $categoryConfig,
        PageConfig $pageConfig,
        ResponseInterface $response
    ) {
        $this->stateService     = $stateService;
        $this->productConfig    = $productConfig;
        $this->categoryConfig   = $categoryConfig;
        $this->pageConfig       = $pageConfig;
        $this->response         = $response;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterRenderResult($subject, $result)
    {
        $this->removeProductSnippets($this->response);
        $this->removeCategorySnippets($this->response);
        $this->removeHomeSnippets($this->response);

        return $result;
    }

    /**
     * @param ResponseInterface $response
     */
    private function removeProductSnippets(ResponseInterface $response)
    {
        if (!$this->stateService->isProductPage()) {
            return;
        }

        if (!$this->productConfig->isRemoveNativeRs()) {
            return;
        }

        $body = $response->getBody();

        $body = $this->deleteWrongSnippets($body);

        $response->setBody($body);
    }

    /**
     * @param ResponseInterface $response
     */
    private function removeCategorySnippets(ResponseInterface $response)
    {
        if (!$this->stateService->isCategoryPage()) {
            return;
        }

        if (!$this->categoryConfig->isRemoveNativeRs()) {
            return;
        }

        $body = $response->getBody();

        $body = $this->deleteWrongSnippets($body);

        $response->setBody($body);
    }

    /**
     * @param ResponseInterface $response
     */
    private function removeHomeSnippets(ResponseInterface $response)
    {
        if (!$this->stateService->isHomePage()) {
            return;
        }

        if (!$this->pageConfig->isRemoveNativeRs()) {
            return;
        }

        $body = $response->getBody();

        $body = $this->deleteWrongSnippets($body);

        $response->setBody($body);
    }

    /**
     * Remove itemprop, itemscope from breadcrumbs html
     *
     * @param string $html
     *
     * @return array|string|null
     */
    public function deleteWrongSnippets($html)
    {
        $crumbsPattern = '/\\<span class="breadcrumbsbefore"\\>\\<\\/span\\>(.*?)'
            . '\\<span class="breadcrumbsafter"\\>\\<\\/span\\>/ims';
        preg_match($crumbsPattern, $html, $crumbs);

        $pattern = [
            '/itemprop="(.*?)"/ims',
            '/itemprop=\'(.*?)\'/ims',
            '/itemtype="(.*?)"/ims',
            '/itemtype=\'(.*?)\'/ims',
            '/itemscope="(.*?)"/ims',
            '/itemscope=\'(.*?)\'/ims',
            '/itemscope=\'\'/ims',
            '/itemscope=""/ims',
            '/itemscope\s/ims',
        ];

        $html = preg_replace($pattern, '', $html);

        if (isset($crumbs[1]) && $crumbs[1]) {
            $html = preg_replace($crumbsPattern, $crumbs[1], $html);
        }

        return $html;
    }
}

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



namespace Mirasvit\Seo\Plugin\Frontend\Framework\Controller\Result;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\LayoutInterface;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\Seo\Model\Config;

class AddPaginationLinkPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var StateServiceInterface
     */
    private $stateService;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * AddPaginationLinkPlugin constructor.
     * @param Config $config
     * @param StateServiceInterface $stateService
     * @param ResponseInterface $response
     * @param LayoutInterface $layout
     */
    public function __construct(
        Config $config,
        StateServiceInterface $stateService,
        ResponseInterface $response,
        LayoutInterface $layout
    ) {
        $this->config       = $config;
        $this->response     = $response;
        $this->stateService = $stateService;
        $this->layout       = $layout;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterRenderResult($subject, $result)
    {
        if (!$this->config->isPagingPrevNextEnabled()) {
            return $result;
        }
        \Magento\Framework\Profiler::start(__METHOD__);
        if (!$this->stateService->isCategoryPage()) {
            \Magento\Framework\Profiler::stop(__METHOD__);
            return $result;
        }

        $pager = $this->getPagerBlock();

        if (!$pager) {
            \Magento\Framework\Profiler::stop(__METHOD__);
            return $result;
        }

        $numPages = count($pager->getPages());

        if ($numPages <= 1) {
            \Magento\Framework\Profiler::stop(__METHOD__);
            return $result;
        }

        $previousPageUrl = $pager->getPreviousPageUrl();
        $nextPageUrl     = $pager->getNextPageUrl();

        if (!$pager->isFirstPage() && !$pager->isLastPage()) {
            $this->addLink('prev', $previousPageUrl);
            $this->addLink('next', $nextPageUrl);
        } elseif ($pager->isFirstPage()) {
            $this->addLink('next', $nextPageUrl);
        } elseif ($pager->isLastPage()) {
            $this->addLink('prev', $previousPageUrl);
        }
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $result;
    }

    /**
     * @return false|\Magento\Theme\Block\Html\Pager
     */
    private function getPagerBlock()
    {
        $block = $this->layout->getBlock('product_list_toolbar_pager');

        if ($block
            && $block instanceof \Magento\Theme\Block\Html\Pager
            && $block->getCollection()) {
            return $block;
        }

        return false;
    }

    /**
     * @param string $type
     * @param string $url
     *
     * @return void
     */
    private function addLink($type, $url)
    {
        $body = $this->response->getBody();

        $link = '<link rel="' . $type . '" href="' . htmlspecialchars($url) . '" />';

        $pattern     = '/<\/head>/ims';
        $replacement = PHP_EOL . $link . '</head>';

        $body = preg_replace($pattern, $replacement, $body, 1);

        $this->response->setBody($body);
    }
}

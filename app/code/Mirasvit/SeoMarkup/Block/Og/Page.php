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

namespace Mirasvit\SeoMarkup\Block\Og;

use Magento\Cms\Helper\Page as CmsHelper;
use Magento\Cms\Model\Page as CmsPage;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\PageConfig;

class Page extends AbstractBlock
{
    private $cmsPage;

    private $cmsHelper;

    private $config;

    private $stateService;

    private $logo;

    public function __construct(
        PageConfig $pageConfig,
        StateServiceInterface $stateService,
        Logo $logo,
        Template\Context $context,
        CmsPage $cmsPage,
        CmsHelper $cmsHelper
    ) {
        $this->config       = $pageConfig;
        $this->stateService = $stateService;
        $this->logo         = $logo;
        $this->cmsPage      = $cmsPage;
        $this->cmsHelper    = $cmsHelper;

        parent::__construct($context);
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getMeta(): ?array
    {
        if (!$this->config->isOgEnabled()) {
            return null;
        }

        if ($this->cmsPage->getOpenGraphImageUrl()) {
            $ogImage = $this->cmsPage->getOpenGraphImageUrl();
        } else {
            $ogImage = $this->logo->getLogoSrc();
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeManager->getStore();

        return [
            'og:type'        => $this->stateService->isHomePage() ? 'website' : 'article',
            'og:url'         => $this->_urlBuilder->escape($this->getPageUrl((int)$this->cmsPage->getId())),
            'og:title'       => $this->pageConfig->getTitle()->get(),
            'og:description' => $this->pageConfig->getDescription(),
            'og:image'       => $ogImage,
            'og:site_name'   => $store->getFrontendName(),
        ];
    }

    private function getPageUrl(int $pageId): ?string
    {
        return $this->cmsHelper->getPageUrl($pageId) ?: null;
    }
}

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

use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;

class Category extends AbstractBlock
{
    private $categoryConfig;

    private $stateService;

    private $logo;

    public function __construct(
        CategoryConfig $categoryConfig,
        StateServiceInterface $stateService,
        Logo $logo,
        Template\Context $context
    ) {
        $this->categoryConfig = $categoryConfig;
        $this->stateService   = $stateService;
        $this->logo           = $logo;

        parent::__construct($context);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getMeta(): ?array
    {
        if (!$this->categoryConfig->isOgEnabled()) {
            return null;
        }

        $category = $this->stateService->getCategory();

        if (!$category) {
            return null;
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeManager->getStore();

        $meta = [
            'og:type'        => 'product.group',
            'og:url'         => $this->_urlBuilder->escape($category->getUrl()),
            'og:title'       => $this->pageConfig->getTitle()->get(),
            'og:description' => $this->pageConfig->getDescription(),
            'og:image'       => $category->getImageUrl() ? $category->getImageUrl() : $this->logo->getLogoSrc(),
            'og:site_name'   => $store->getFrontendName(),
        ];

        return $meta;
    }
}

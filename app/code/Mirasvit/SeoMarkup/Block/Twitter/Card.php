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

namespace Mirasvit\SeoMarkup\Block\Twitter;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;
use Mirasvit\SeoMarkup\Block\Og\AbstractBlock;
use Mirasvit\SeoMarkup\Model\Config\TwitterConfig;

class Card extends AbstractBlock
{
    private $twitterConfig;

    private $logo;

    private $registry;

    private $imageHelper;

    public function __construct(
        TwitterConfig $twitterConfig,
        Registry $registry,
        Logo $logo,
        ImageHelper $imageHelper,
        Template\Context $context
    ) {
        $this->twitterConfig = $twitterConfig;
        $this->registry      = $registry;
        $this->logo          = $logo;
        $this->imageHelper   = $imageHelper;

        parent::__construct($context);
    }

    protected function getMetaOptionKey(): string
    {
        return 'name';
    }

    protected function getMeta(): ?array
    {
        $storeId = $this->_storeManager->getStore()->getId();
        if (!$this->twitterConfig->getCardType() || !$this->twitterConfig->getUsername($storeId)) {
            return null;
        }

        $cardType = $this->twitterConfig->getCardType();
        $username = $this->twitterConfig->getUsername($storeId);

        if (strpos($username, '@') !== 0) {
            $username = '@' . $username;
        }

        $meta = [
            'twitter:card'        => $cardType === TwitterConfig::CARD_TYPE_SMALL_IMAGE
                ? "summary" : "summary_large_image",
            'twitter:site'        => $username,
            'twitter:creator'     => $username,
            'twitter:title'       => $this->pageConfig->getTitle()->get(),
            'twitter:url'         => $this->_urlBuilder->escape($this->getCurrentUrl()),
            'twitter:description' => $this->pageConfig->getDescription(),
            'twitter:image'       => $this->getImage(),
        ];

        return $meta;
    }

    public function getImage(): ?string
    {
        $fullActionCode = $this->_request->getFullActionName();

        switch ($fullActionCode) {
            case 'catalog_product_view':
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->registry->registry('current_product');
                if ($product && $product->getData('image') != 'no_selection') {
                    return $this->imageHelper
                        ->init($product, 'product_page_image_medium')
                        ->getUrl();
                }
                break;

            case 'catalog_category_view':
                /** @var \Magento\Catalog\Model\Category $category */
                $category = $this->registry->registry('current_category');

                return $category->getImageUrl() ?: null;
        }

        return $this->logo->getLogoSrc() ?: null;
    }

    private function getCurrentUrl(): string
    {
        $url = $this->_urlBuilder->getCurrentUrl();
        $url = strtok($url, '?');

        return $url;
    }
}

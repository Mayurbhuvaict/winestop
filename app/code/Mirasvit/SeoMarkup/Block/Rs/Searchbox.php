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



namespace Mirasvit\SeoMarkup\Block\Rs;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\SeoMarkup\Model\Config\SearchboxConfig;

class Searchbox extends Template
{
    /**
     * @var SearchboxConfig
     */
    private $searchboxConfig;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param SearchboxConfig $searchboxConfig
     */
    public function __construct(
        Context $context,
        SearchboxConfig $searchboxConfig
    ) {
        $this->searchboxConfig = $searchboxConfig;

        parent::__construct($context);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->searchboxConfig->getSearchBoxType()) {
            return false;
        }

        $data = $this->getJsonData();

        return '<script type="application/ld+json">' . \Zend_Json::encode($data) . '</script>';
    }

    /**
     * @return array
     */
    private function getJsonData()
    {
        $data = [
            "@context"        => "https://schema.org",
            "@type"           => "WebSite",
            "url"             => $this->getBaseUrl(),
            "potentialAction" => [
                "@type"       => "SearchAction",
                "target"      => $this->getTarget(),
                "query-input" => "required name=search_term_string",
            ],
        ];

        return $data;
    }

    /**
     * @return string
     */
    private function getTarget()
    {
        $searchboxType = $this->searchboxConfig->getSearchBoxType();

        switch ($searchboxType) {
            case (SearchboxConfig::SEARCH_BOX_TYPE_CATALOG_SEARCH):
                $target = $this->getBaseUrl() . 'catalogsearch/result?q={search_term_string}';
                break;

            case (SearchboxConfig::SEARCH_BOX_TYPE_BLOG_SEARCH):
                $target = $this->getBaseUrl() . $this->searchboxConfig->getBlogSearchUrl() . '{search_term_string}';
                break;
        }

        return $target;
    }
}

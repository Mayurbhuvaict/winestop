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



namespace Mirasvit\Seo\Service\Checklist;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Model\Config;
use Mirasvit\Seo\Model\Config\ImageConfig;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class ChecklistTestsService
{
    private $config;

    private $productConfig;

    private $categoryConfig;

    private $imageConfig;

    private $urlBuilder;

    private $storeManager;

    private $moduleManager;

    private $homePageUrl;

    private $homePageMarkup;

    private $robotsTxtContent;

    public function __construct(
        Config $config,
        ProductConfig $productConfig,
        CategoryConfig $categoryConfig,
        ImageConfig $imageConfig,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        ModuleManager $moduleManager
    ) {
        $this->config         = $config;
        $this->productConfig  = $productConfig;
        $this->categoryConfig = $categoryConfig;
        $this->imageConfig    = $imageConfig;
        $this->urlBuilder     = $urlBuilder;
        $this->storeManager   = $storeManager;
        $this->moduleManager  = $moduleManager;

        $this->homePageUrl      = $this->storeManager->getStore()->getBaseUrl();
        $this->homePageMarkup   = file_get_contents($this->storeManager->getStore()->getBaseUrl());
        $this->robotsTxtContent = trim(file_get_contents(rtrim($this->storeManager->getStore()->getBaseUrl(), '/') . '/robots.txt'));
    }

    public function resolve(string $testKey)
    {
        return $this->{$testKey}();
    }

    private function robotsTxtIndexableTest()
    {
        $status  = true;
        $message = '';

        if (empty($this->robotsTxtContent)) {
            $status  = false;
            $message = '<p>The robots.txt file is empty.</p><p>Please follow
                <a target="_blank" href="https://developers.google.com/search/docs/advanced/robots/create-robots-txt">the instructions</a>
                to configure your robots.txt as well</p>';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function robotsTxtIndexableHint()
    {
        $hint = 'Store can`t be indexed if it closed for robots and crawlers.';

        return $hint;
    }

    private function homepageMetaTagsExistsTest()
    {
        $requiredMetaTags = [
            'title'       => 'Meta Title',
            'keywords'    => 'Meta Keywords',
            'description' => 'Meta Description',
        ];

        $status      = true;
        $message     = '';
        $missingTags = array_diff(array_keys($requiredMetaTags), array_keys(get_meta_tags($this->homePageUrl)));

        if (!empty($missingTags)) {
            $metaTags = [];

            foreach ($missingTags as $key) {
                $metaTags[] = $requiredMetaTags[$key];
            }

            $status  = false;
            $message = '<p>Missing the following Meta Tags on Homepage: ' . implode(', ', $metaTags) . '.</p><p>Please configure your Homepage Meta Tags in  Content > Pages > Home Page > Search Engine Optimization</p>';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function homepageMetaTagsExistsHint()
    {
        $hint = 'Needs to show correct title and store description in the search results.';

        return $hint;
    }

    private function robotsTxtSEOSitemapExistsTest()
    {
        $status  = true;
        $message = '';

        if (empty($this->robotsTxtContent)) {
            $status  = false;
            $message = 'Unable to run test, the robots.txt file is empty';
        } elseif (!preg_match('/Sitemap\:/i', $this->robotsTxtContent)) {
            $status  = false;
            $message = '<p>Sitemap is not set in the robots.txt file.</p><p>Please follow
                <a target="_blank" href="https://developers.google.com/search/docs/advanced/robots/create-robots-txt"> this guide </a>
                to know more about robots.txt configuration</p>';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function robotsTxtSEOSitemapExistsHint()
    {
        $hint = '<p>A sitemap is a file where you provide information about the pages, videos, and other files on your site, and the relationships between them.</p><p>Search engines like Google read this file to more intelligently crawl your site.</p>';

        return $hint;
    }

    private function GoogleAnalyticsTagAppliedTest()
    {
        preg_match('/i,s,o,g,r,a,m|GoogleAnalyticsObject|Magento_GoogleAnalytics/', $this->homePageMarkup, $match);
        $status  = true;
        $message = '';

        if (empty($match)) {
            $status  = false;
            $message = '<p>Unfortunately, the Google Analytics Tag is not applied for your store.</p><p>Please follow
                <a target="_blank" href="https://docs.magento.com/user-guide/marketing/google-universal-analytics.html"> steps described here </a>
                to make it work as well</p>';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function GoogleAnalyticsTagAppliedHint()
    {
        $hint = 'Get a deeper understanding of your customers. Google Analytics gives you the free tools you need to analyze data for your business in one place.';

        return $hint;
    }

    private function HTMLSitemapExistsTest()
    {
        $status  = (bool) $this->moduleManager->isOutputEnabled('Mirasvit_SeoSitemap');
        $message = '';

        if (!$status) {
            $message = 'Please enable the Mirasvit_SeoSitemap module running the following command: <pre>bin/magento module:enable Mirasvit_SeoSitemap</pre>';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function HTMLSitemapExistsHint()
    {
        $hint = '<p>An HTML sitemap is an HTML page on which all subpages of a website are listed. It is usually linked in the footer of a site and is therefore visible to all visitors.</p>
            <p>In contrast to XML sitemaps, HTML sitemaps are mainly created for users, as they help them to get an overview of the structure of your site and to navigate through all the subpages. Visually, an HTML sitemap resembles a navigation bar where all tabs are opened and where you can click on each link to get to the corresponding subpage.</p>';

        return $hint;
    }

    private function ProductsRSExistsTest()
    {
        $status  = (bool)$this->productConfig->isRsEnabled($this->storeManager->getStore()->getId());
        $message = '';

        if (!$status) {
            $url     = $this->urlBuilder->getUrl('admin/system_config/edit/section/seo/', []);
            $message = 'Please enable the "Enable Rich Snippet" function in <a target="_blank" href="' . $url . '"> Advanced SEO Suite -> Settings</a>, SEO Rich Snippets and Opengraph -> Product Page section';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function ProductsRSExistsHint()
    {
        $hint = 'Enhanced search results that provide additional information to searchers on the contents of a page. Google and other search engines use structured data, contained within the HTML, or Javascript of a webpage, to form these snippets.';

        return $hint;
    }

    private function CategoriesRSExistsTest()
    {
        $status  = (bool)$this->categoryConfig->isRsEnabled($this->storeManager->getStore()->getId());
        $message = '';

        if (!$status) {
            $url     = $this->urlBuilder->getUrl('admin/system_config/edit/section/seo/', []);
            $message = 'Please enable the "Enable Rich Snippet" function in <a target="_blank" href="' . $url . '"> Advanced SEO Suite -> Settings</a>, SEO Rich Snippets and Opengraph -> Category Page section';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function CategoriesRSExistsHint()
    {
        $hint = 'Enhanced search results that provide additional information to searchers on the contents of a page. Google and other search engines use structured data, contained within the HTML, or Javascript of a webpage, to form these snippets.';

        return $hint;
    }

    private function SEOFriendlyImageURLEnabledTest()
    {
        $status  = (bool)$this->imageConfig->isFriendlyUrlEnabled();
        $message = '';

        if (!$status) {
            $url     = $this->urlBuilder->getUrl('admin/system_config/edit/section/seo/', []);
            $message = 'Please enable the "Enable SEO-friendly URLs for Product Images" function in <a target="_blank" href="' . $url . '"> Advanced SEO Suite -> Settings</a>, Images Settings section';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function SEOFriendlyImageURLEnabledHint()
    {
        $hint = 'Using product names or other variables as image filename as it is most respectively for robots and crawlers then random characters or numbers.';

        return $hint;
    }

    private function SEOFriendlyAltEnabledTest()
    {
        $status  = (bool)$this->imageConfig->isFriendlyAltEnabled();
        $message = '';

        if (!$status) {
            $url     = $this->urlBuilder->getUrl('admin/system_config/edit/section/seo/', []);
            $message = 'Please enable the "Enable generation of Product Images Alt and Title" function in <a target="_blank" href="' . $url . '">Advanced SEO Suite -> Settings</a>, Images Settings section';
        }

        return ['status' => $status, 'message' => $message];
    }

    private function SEOFriendlyAltEnabledHint()
    {
        $hint = 'Alt tags provide a text alternative for an image for search engines and those using screen readers to access a web page.';

        return $hint;
    }
}

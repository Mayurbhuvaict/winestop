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


namespace Mirasvit\SeoAudit\Parser;


use Mirasvit\SeoAudit\Api\Data\UrlInterface;

class PageParser extends AbstractParser
{
    public function retriveUrls(UrlInterface $url, int $jobId): void
    {
        if (!$url->getContent()) {
            return;
        }

        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($url->getContent());
        libxml_clear_errors();

        $this->retrievePageLinks($dom, $url, $jobId);
        $this->retrieveLinksFromHead($dom, $url, $jobId);
        $this->retrieveMediaLinks($dom, $url, $jobId);
        $this->retrieveIframeLinks($dom, $url, $jobId);
    }

    private function retrievePageLinks(
        \DOMDocument $dom,
        UrlInterface $url,
        int $jobId
    ): void {
        $urlInfo = parse_url($url->getUrl());
        $baseUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'];

        $links = $dom->getElementsByTagName('a');

        /** @var \DOMElement $link */
        foreach ($links as $link) {
            $l = $link->getAttribute('href');

            $l = $this->resolveUrl($l, $baseUrl);

            $this->urlService->ensureUrl($l, UrlInterface::TYPE_PAGE, $jobId, $url->getId());
        }
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function retrieveLinksFromHead(
        \DOMDocument $dom,
        UrlInterface $url,
        int $jobId
    ): void {
        $urlInfo = parse_url($url->getUrl());
        $baseUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'];

        $headLinks = $dom->getElementsByTagName('link');

        /** @var \DOMElement $headLink */
        foreach ($headLinks as $headLink) {
            $l = $headLink->getAttribute('href');

            if (!$l) {
                continue;
            }

            $l   = $this->resolveUrl($l, $baseUrl);
            $rel = strtolower($headLink->getAttribute('rel'));

            if (!$rel) {
                continue;
            }

            if ($rel == 'stylesheet') {
                $this->urlService->ensureUrl($l, UrlInterface::TYPE_CSS, $jobId, $url->getId());

                continue;
            }

            if (strpos($rel, 'icon') !== false) {
                $this->urlService->ensureUrl($l, UrlInterface::TYPE_IMAGE, $jobId, $url->getId());

                continue;
            }

            if ($rel == 'preload' || $rel == 'dns-prefetch') {
                $type = $headLink->getAttribute('as');

                switch ($type) {
                    case 'style':
                        $this->urlService->ensureUrl($l, UrlInterface::TYPE_CSS, $jobId, $url->getId());
                        break;
                    case 'script':
                        $this->urlService->ensureUrl($l, UrlInterface::TYPE_JS, $jobId, $url->getId());
                        break;
                    case 'font':
                        $this->urlService->ensureUrl($l, UrlInterface::TYPE_FONT, $jobId, $url->getId());
                        break;
                    case 'image':
                        $this->urlService->ensureUrl($l, UrlInterface::TYPE_IMAGE, $jobId, $url->getId());
                        break;
                    default:
                        break;
                }
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function retrieveMediaLinks(
        \DOMDocument $dom,
        UrlInterface $url,
        int $jobId
    ): void {
        $urlInfo = parse_url($url->getUrl());
        $baseUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'];

        // get images links
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $image) {
            $l = $image->getAttribute('src');

            if (!$l) {
                continue;
            }

            if (strpos($l, 'data:image') !== false) { // lazyloaded images
                $l = $image->getAttribute('data-src');

                if (!$l) {
                    continue;
                }
            }

            $l = $this->resolveUrl($l, $baseUrl);

            $this->urlService->ensureUrl($l, UrlInterface::TYPE_IMAGE, $jobId, $url->getId());
        }

        // links from <source> tag
        $sources = $dom->getElementsByTagName('source');

        foreach ($sources as $source) {
            $l = $source->getAttribute('srcset');

            if (!$l) {
                continue;
            }

            $l = $this->resolveUrl($l, $baseUrl);

            $type = $source->getAttribute('type');
            $type = explode('/', $type)[0];
            $type = strtolower($type);

            switch ($type) {
                case 'image':
                    $this->urlService->ensureUrl($l, UrlInterface::TYPE_IMAGE, $jobId, $url->getId());
                    break;
                case 'audio':
                    $this->urlService->ensureUrl($l, UrlInterface::TYPE_AUDIO, $jobId, $url->getId());
                    break;
                case 'video':
                    $this->urlService->ensureUrl($l, UrlInterface::TYPE_VIDEO, $jobId, $url->getId());
                    break;
                default:
                    break;
            }
        }
    }

    private function retrieveIframeLinks(
        \DOMDocument $dom,
        UrlInterface $url,
        int $jobId
    ): void {
        $urlInfo = parse_url($url->getUrl());
        $baseUrl = $urlInfo['scheme'] . '://' . $urlInfo['host'];

        $iframes = $dom->getElementsByTagName('iframe');

        foreach ($iframes as $iframe) {
            $l = $iframe->getAttribute('src');

            if (!$l) {
                continue;
            }

            $l = $this->resolveUrl($l, $baseUrl);

            $this->urlService->ensureUrl($l, UrlInterface::TYPE_PAGE, $jobId, $url->getId());
        }
    }

    private function resolveUrl(string $url, string $baseUrl): string
    {
        if (strpos($url, "/") === 0) {
            $url = $baseUrl.$url;
        }

        return $url;
    }
}

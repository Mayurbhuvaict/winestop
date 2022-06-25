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


use Magento\Framework\Xml\Parser as XmlParser;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Repository\UrlRepoitory;
use Mirasvit\SeoAudit\Service\UrlService;

class SitemapParser extends AbstractParser
{
    private $parser;

    public function __construct(
        XmlParser $parser,
        UrlRepoitory $urlRepoitory,
        UrlService $urlService
    ) {
        $this->parser = $parser;

        parent::__construct($urlRepoitory, $urlService);
    }

    public function retriveUrls(UrlInterface $url, int $jobId): void
    {
        $xmlDom = null;

        try {
            $xmlDom = $this->parser->loadXML($url->getContent())->getDom();
        } catch (\Exception $e) {
            $url->setStatus(UrlInterface::STARUS_ERROR);

            $this->urlRepository->save($url);

            return;
        }

        if (!$xmlDom) {
            $url->setStatus(UrlInterface::STATUS_CRAWLED);

            $this->urlRepository->save($url);

            return;
        }

        $isSegmented = count($xmlDom->getElementsByTagName('sitemapindex'));

        if ($isSegmented) {
            $sitemaps = $xmlDom->getElementsByTagName('loc');

            foreach ($sitemaps as $sitemap) {
                $this->urlService->ensureUrl($sitemap->nodeValue, UrlInterface::TYPE_SITEMAP, $jobId, $url->getId());
            }

            $url->setStatus(UrlInterface::STATUS_CRAWLED);

            $this->urlRepository->save($url);

            return; // no need to further parsing segmented sitemap
        }

        $links = $xmlDom->getElementsByTagName('loc');

        /** @var \DOMElement $link */
        foreach ($links as $link) {
            //need this because getElementsByTagName('loc') returns all nodes with 'loc' part in the node name
            if ($link->nodeName !== 'loc') {
                continue;
            }

            $this->urlService->ensureUrl($link->nodeValue, UrlInterface::TYPE_PAGE, $jobId, $url->getId());
        }

        $images = $xmlDom->getElementsByTagName('image:loc');

        /** @var \DOMElement $image */
        foreach ($images as $image) {
            $this->urlService->ensureUrl($image->nodeValue, UrlInterface::TYPE_IMAGE, $jobId, $url->getId());
        }

        $videos = $xmlDom->getElementsByTagName('video:content_loc');

        /** @var \DOMElement $video */
        foreach ($videos as $video) {
            $this->urlService->ensureUrl($video->nodeValue, UrlInterface::TYPE_VIDEO, $jobId, $url->getId());
        }
    }
}

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


namespace Mirasvit\SeoAudit\Service;


use Magento\Store\Model\StoreManagerInterfaceFactory;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Repository\UrlRepoitory;

class UrlService
{
    private $parseableTypes = [
        UrlInterface::TYPE_PAGE,
        UrlInterface::TYPE_SITEMAP,
        UrlInterface::TYPE_ROBOTS
    ];

    private $urlRepository;

    private $storeManagerFactory;
    
    private $adminUrl;

    public function __construct(
        UrlRepoitory $urlRepoitory,
        StoreManagerInterfaceFactory $storeManagerFactory,
        \Magento\Backend\Model\UrlInterface $adminUrl
    ) {
        $this->urlRepository       = $urlRepoitory;
        $this->storeManagerFactory = $storeManagerFactory;
        $this->adminUrl            = $adminUrl;
    }

    public function loadResource(UrlInterface $url): UrlInterface
    {
        $redirectStatuses = [301, 302];

        $content = '';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url->getUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);

        $content        = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $url->setStatusCode($httpStatusCode);

        if (in_array((int)$httpStatusCode, $redirectStatuses)) {
            $url->setType(UrlInterface::TYPE_REDIRECT);
        }

        if (
            in_array($url->getType(), $this->parseableTypes)
            && !$this->isExternalUrl($url->getUrl())
            && $content
            && $httpStatusCode == 200
        ) {
            $url->setContent($content);

            $url = $this->retrieveMeta($url);
        }

        return $this->urlRepository->save($url);
    }

    public function ensureUrl(string $url, string $type, int $jobId, int $parentId = null): ?UrlInterface
    {
        $url = $this->normalizeUrl($url);

        if (!$url || preg_match('/(mailto|tel|javascript):/is', $url) || $this->isAdminAreaUrl($url)) {
            return null;
        }

        $urlObject = $this->urlRepository->getByUrl($url);

        // create new URL object if not exist
        if (!$urlObject) {
            $urlObject = $this->urlRepository->create();

            $urlObject->setUrl($url)
                ->setUrlHash(sha1($url))
                ->setType($type);
        }

        if (!$urlObject->getStatus()) {
            $urlObject->setStatus(UrlInterface::STATUS_PENDING);
        }

        $urlObject->setJobId($jobId);

        if ($parentId) {
            $parentIds = $urlObject->getParentIds();

            $parentIds[] = $parentId;

            $parentIds = array_unique($parentIds);
            asort($parentIds);

            $urlObject->setParentIds($parentIds);
        } elseif (!count($urlObject->getParentIds())) {
            $urlObject->setParentIds([0]);
        }

        return $this->urlRepository->save($urlObject);
    }

    public function isExternalUrl(string $url): bool
    {
        $storeManager = $this->storeManagerFactory->create();

        /** @var \Magento\Store\Model\Store $store */
        foreach ($storeManager->getStores() as $store) {
            $baseUrl = $store->getBaseUrl();
            $baseUrl = substr($baseUrl, 0, strlen($baseUrl) - 1);

            $secureBaseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true);
            $secureBaseUrl = substr($secureBaseUrl, 0, strlen($secureBaseUrl) - 1);

            if (strpos($url, $baseUrl) !== false || strpos($url, $secureBaseUrl) !== false) {
                return false;
            }
        }

        return true;
    }

    private function isAdminAreaUrl(string $url): bool
    {
        $baseAdminUrl = $this->adminUrl->getUrl('admin');
        $baseAdminUrl = str_replace('/admin/', '', $baseAdminUrl);

        return strpos($url, $baseAdminUrl) !== false;
    }

    private function normalizeUrl(string $url): ?string
    {
        if (strpos($url, '#') !== false) {
            $url = substr($url, 0, strpos($url, '#'));
        }

        if (!$url || strpos($url, 'customer/account') !== false) {
            return null;
        }

        if (preg_match('@https?://@is', $url, $match)) {
            $host = $match[0];

            $url = str_replace($host, '', $url);
            $url = preg_replace('@/{2,}@s', '/', $url);

            if (strpos($url, '/') === 0) {
                $url = substr($url, 1);
            }

            $url = $host . $url;
        }

        // replace part of the URL like aHR0cDovL3NlbzI0Mi5scmcubWlyYXN2aXQuY29tLw%2C%2C (min 48 chars)
        // with #hashkey so we still can request the URL
        $url = preg_replace('@[a-z0-9\%]{48,}@is', '#hashkey', $url);

        if (strrpos($url, '/') == strlen($url) - 1) {
            $url = substr($url, 0, strlen($url) - 1);
        }

        return $url;
    }

    private function retrieveMeta(UrlInterface $url): UrlInterface
    {
        if (!$url->getContent()) {
            return $url;
        }

        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($url->getContent());
        libxml_clear_errors();

        $meta = $dom->getElementsByTagName('meta');

        /** @var \DOMElement $m */
        foreach ($meta as $m) {
            switch ($m->getAttribute('name')) {
                case 'title':
                    $url->setMetaTitle($m->getAttribute('content'));
                    break;
                case 'description':
                    $url->setMetaDescription($m->getAttribute('content'));
                    break;
                case 'robots':
                    $url->setRobots(strtoupper($m->getAttribute('content')));
                    break;
                default:
                    break;
            }
        }

        $links = $dom->getElementsByTagName('link');

        /** @var \DOMElement $link */
        foreach ($links as $link) {
            $rel = $link->getAttribute('rel');

            if ($rel && $rel === UrlInterface::CANONICAL) {
                $url->setCanonical($link->getAttribute('href'));
                break;
            }
        }

        return $url;
    }

    public function resetUrls(JobInterface $job): void
    {
        $resource   = $this->urlRepository->getCollection()->getResource();
        $connection = $resource->getConnection();

        $deleteQuery = "DELETE FROM {$resource->getTable(UrlInterface::TABLE_NAME)}
                        WHERE status = 'pending'";

        $updateQuery = "UPDATE {$resource->getTable(UrlInterface::TABLE_NAME)}
                        SET status = 'pending', job_id = {$job->getId()}
                        WHERE status = 'finished'";

        $connection->query($deleteQuery);
        $connection->query($updateQuery);
    }

    public function getResourcePageTypes(): array
    {
        return [
            UrlInterface::TYPE_CSS,
            UrlInterface::TYPE_JS,
            UrlInterface::TYPE_FONT,
            UrlInterface::TYPE_IMAGE,
            UrlInterface::TYPE_AUDIO,
            UrlInterface::TYPE_VIDEO
        ];
    }
}

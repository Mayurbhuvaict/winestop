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


namespace Mirasvit\SeoAudit\Model;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;

class Url extends AbstractModel implements UrlInterface
{
    public function getId(): int
    {
        return (int)$this->getData(self::ID);
    }

    public function getParentIds(): array
    {
        $parentIds = $this->getData(self::PARENT_IDS);

        if (!$parentIds) {
            return [];
        }

        $parentIds = explode(',', $parentIds);

        $parentIds = array_map(function ($id) {
            return (int)$id;
        }, $parentIds);

        return $parentIds;
    }

    public function setParentIds(array $parentIds): UrlInterface
    {
//        $parentIds = array_filter($parentIds, function ($id) {
//            return is_numeric($id);
//        });
        $parentIds = implode(',', $parentIds);

        return $this->setData(self::PARENT_IDS, $parentIds);
    }

    public function getJobId(): int
    {
        return (int)$this->getData(self::JOB_ID);
    }

    public function setJobId(int $jobId): UrlInterface
    {
        return $this->setData(self::JOB_ID, $jobId);
    }

    public function getUrl(): string
    {
        return (string)$this->getData(self::URL);
    }

    public function setUrl(string $url): UrlInterface
    {
        return $this->setData(self::URL, $url);
    }

    public function getUrlHash(): string
    {
        return (string)$this->getData(self::URL_HASH);
    }

    public function setUrlHash(string $urlHash): UrlInterface
    {
        return $this->setData(self::URL_HASH, $urlHash);
    }

    public function getStatusCode(): int
    {
        return (int)$this->getData(self::STATUS_CODE);
    }

    public function setStatusCode(int $code): UrlInterface
    {
        return $this->setData(self::STATUS_CODE, $code);
    }

    public function getType(): string
    {
        return (string)$this->getData(self::TYPE);
    }

    public function setType(string $type): UrlInterface
    {
        return $this->setData(self::TYPE, $type);
    }

    public function getContent(): ?string
    {
        $content = $this->getData(self::CONTENT);

        return $content ? (string)$content : null;
    }

    public function setContent(string $content = null): UrlInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    public function getMetaTitle(): string
    {
        return (string)$this->getData(self::META_TITLE);
    }

    public function setMetaTitle(string $metaTitle): UrlInterface
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    public function getMetaDescription(): string
    {
        return (string)$this->getData(self::META_DESCRIPTION);
    }

    public function setMetaDescription(string $metaDescription): UrlInterface
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    public function getRobots(): ?string
    {
        return $this->getData(self::ROBOTS) ?: null;
    }

    public function setRobots(string $robots): UrlInterface
    {
        return $this->setData(self::ROBOTS, $robots);
    }

    public function getCanonical(): ?string
    {
        return $this->getData(self::CANONICAL) ?: null;
    }

    public function setCanonical(string $canonical): UrlInterface
    {
        return $this->setData(self::CANONICAL, $canonical);
    }

    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    public function setStatus(string $checkStatus): UrlInterface
    {
        return $this->setData(self::STATUS, $checkStatus);
    }
}

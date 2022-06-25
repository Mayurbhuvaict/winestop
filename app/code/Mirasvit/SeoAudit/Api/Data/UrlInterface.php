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


namespace Mirasvit\SeoAudit\Api\Data;


interface UrlInterface
{
    const TABLE_NAME = 'mst_seo_audit_url';

    const ID               = 'url_id';
    const PARENT_IDS       = 'parent_ids';
    const JOB_ID           = 'job_id';
    const URL              = 'url';
    const URL_HASH         = 'url_hash';
    const STATUS_CODE      = 'status_code';
    const TYPE             = 'type';
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const CANONICAL        = 'canonical';
    const ROBOTS           = 'robots';
    const CONTENT          = 'content';
    const STATUS           = 'status';

    const STATUS_PENDING    = 'pending';
    const STATUS_CRAWLED    = 'crawled';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FINISHED   = 'finished';
    const STARUS_ERROR      = 'error';

    const TYPE_PAGE     = 'page';
    const TYPE_IMAGE    = 'image';
    const TYPE_VIDEO    = 'video';
    const TYPE_AUDIO    = 'audio';
    const TYPE_JS       = 'js';
    const TYPE_CSS      = 'css';
    const TYPE_FONT     = 'font';
    const TYPE_SITEMAP  = 'sitemap';
    const TYPE_ROBOTS   = 'robots';
    const TYPE_REDIRECT = 'redirect';

    public function getId(): int;

    public function getParentIds(): array;

    public function setParentIds(array $parentId): self;

    public function getJobId() :int;

    public function setJobId(int $jobId): self;

    public function getUrl(): string;

    public function setUrl(string $url): self;

    public function getUrlHash(): string;

    public function setUrlHash(string $urlHash): self;

    public function getStatusCode(): int;

    public function setStatusCode(int $code): self;

    public function getType(): string;

    public function setType(string $type): self;

    public function getContent(): ?string;

    public function setContent(string $content = null): self;

    public function getMetaTitle(): string;

    public function setMetaTitle(string $metaTitle): self;

    public function getMetaDescription(): string;

    public function setMetaDescription(string $metaDescription): self;

    public function getRobots(): ?string;

    public function setRobots(string $robots): self;

    public function getCanonical(): ?string;

    public function setCanonical(string $canonical): self;

    public function getStatus(): string;

    public function setStatus(string $checkStatus): self;
}

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


interface JobInterface
{
    const TABLE_NAME  = 'mst_seo_audit_job';

    const ID                = 'job_id';
    const STATUS            = 'status';
    const MESSAGE           = 'message';
    const CREATED_AT        = 'created_at';
    const STARTED_AT        = 'started_at';
    const FINISHED_AT       = 'finished_at';
    const RESULT_SERIALIZED = 'result_serialized';

    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FINISHED   = 'finished';
    const STATUS_ERROR      = 'error';

    public function getId(): int;

    public function getStatus(): string;

    public function setStatus(string $status): self;

    public function getCreatedAt(): string;

    public function setCreatedAt(string $createdAt): self;

    public function getStartedAt(): ?string;

    public function setStartedAt(string $startedAt): self;

    public function getFinishedAt(): ?string;

    public function setFinishedAt(string $finishedAt): self;

    public function getMessage(): string;

    public function setMessage(string $message): self;

    public function getResult(): ?array;

    public function setResult(array $result): self;
}

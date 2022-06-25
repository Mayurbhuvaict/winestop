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


interface CheckResultInterface
{
    const TABLE_NAME = 'mst_seo_audit_check_result';

    const ID         = 'check_id';
    const URL_ID     = 'url_id';
    const URL_TYPE   = 'url_type';
    const JOB_ID     = 'job_id';
    const IDENTIFIER = 'identifier';
    const IMPORTANCE = 'importance';
    const RESULT     = 'result';
    const VALUE      = 'value';
    const MESSAGE    = 'message';
    const CREATED_AT = 'created_at';

    public function getId(): int;

    public function getUrlId(): int;

    public function setUrlId(int $urlId): self;

    public function getUrlType(): string;

    public function setUrlType(string $type): self;

    public function getJobId(): int;

    public function setJobId(int $jobId): self;

    public function getIdentifier(): string;

    public function setIdentifier(string $identifier): self;

    public function getImportance(): int;

    public function setImportance(int $importance): self;

    public function getValue(): string;

    public function setValue(string $value): self;

    public function getResult(): int;

    public function setResult(int $result): self;

    public function getMessage(): string;

    public function setMessage(string $message): self;

    public function getCreatedAt(): string;
}

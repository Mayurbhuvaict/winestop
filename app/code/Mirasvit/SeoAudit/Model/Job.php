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
use Mirasvit\SeoAudit\Api\Data\JobInterface;

class Job extends AbstractModel implements JobInterface
{
    public function getId(): int
    {
        return (int)$this->getData(self::ID);
    }

    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    public function setStatus(string $status): JobInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): JobInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getStartedAt(): ?string
    {
        $startedAt = $this->getData(self::STARTED_AT);

        return $startedAt ? (string)$startedAt : null;
    }

    public function setStartedAt(string $startedAt): JobInterface
    {
        return $this->setData(self::STARTED_AT, $startedAt);
    }

    public function getFinishedAt(): ?string
    {
        $finishedAt = $this->getData(self::FINISHED_AT);

        return $finishedAt ? (string)$finishedAt : null;
    }

    public function setFinishedAt(string $finishedAt): JobInterface
    {
        return $this->setData(self::FINISHED_AT, $finishedAt);
    }

    public function getMessage(): string
    {
        return (string)$this->getData(self::MESSAGE);
    }

    public function setMessage(string $message): JobInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getResult(): ?array
    {
        $resultSerialized = $this->getData(JobInterface::RESULT_SERIALIZED);

        return $resultSerialized
            ? (array)json_decode($resultSerialized)
            : null;
    }

    public function setResult(array $result): JobInterface
    {
        return $this->setData(JobInterface::RESULT_SERIALIZED, json_encode($result));
    }
}

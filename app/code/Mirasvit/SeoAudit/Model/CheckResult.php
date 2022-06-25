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
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;

class CheckResult extends AbstractModel implements CheckResultInterface
{
    public function getId(): int
    {
        return (int)$this->getData(self::ID);
    }

    public function getUrlId(): int
    {
        return (int)$this->getData(self::URL_ID);
    }

    public function setUrlId(int $urlId): CheckResultInterface
    {
        return $this->setData(self::URL_ID, $urlId);
    }

    public function getUrlType(): string
    {
        return $this->getData(self::URL_TYPE);
    }
    
    public function setUrlType(string $type): CheckResultInterface
    {
        return $this->setData(self::URL_TYPE, $type);
    }

    public function getJobId(): int
    {
        return (int)$this->getData(self::JOB_ID);
    }

    public function setJobId(int $jobId): CheckResultInterface
    {
        return $this->setData(self::JOB_ID, $jobId);
    }

    public function getIdentifier(): string
    {
        return (string)$this->getData(self::IDENTIFIER);
    }

    public function setIdentifier(string $identifier): CheckResultInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    public function getImportance(): int
    {
        return (int)$this->getData(self::IMPORTANCE);
    }

    public function setImportance(int $importance): CheckResultInterface
    {
        return $this->setData(self::IMPORTANCE, $importance);
    }

    public function getResult(): int
    {
        return (int)$this->getData(self::RESULT);
    }

    public function setResult(int $result): CheckResultInterface
    {
        return $this->setData(self::RESULT, $result);
    }
    
    public function getValue(): string
    {
        return $this->getData(self::VALUE);
    }
    
    public function setValue(string $value): CheckResultInterface
    {
        return $this->setData(self::VALUE, $value);
    }

    public function getMessage(): string
    {
        return (string)$this->getData(self::MESSAGE);
    }

    public function setMessage(string $message): CheckResultInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }
}

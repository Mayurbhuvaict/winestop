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

namespace Mirasvit\SeoAudit\Check\HttpStatusCode;

use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Check\AbstractCheck;

class Code404 extends AbstractCheck
{
    public function getAllowedTypes(): array
    {
        return ['all'];
    }

    public function isAllowedForExternal(): bool
    {
        return true;
    }

    public function getIdentifier(): string
    {
        return 'http_status_code_404';
    }

    public function getImportance(): int
    {
        return 1;
    }

    public function getCheckResult(UrlInterface $url): array
    {
        return [
            CheckResultInterface::RESULT  => $url->getStatusCode() == 404 ? self::MIN_SCORE : self::MAX_SCORE,
            CheckResultInterface::VALUE   => $this->encodeValue($url->getStatusCode()),
            CheckResultInterface::MESSAGE => '',
        ];
    }

    public function getValueType(): string
    {
        return self::VALUE_TYPE_INT;
    }

    public function getLabel(): string
    {
        return '404 page';
    }

    public function getGridColumnLabel(): string
    {
        return 'Status Code';
    }

    public function getGridFieldClass(): string
    {
        return 'text-center';
    }

    public function getValueGridOutput(string $value): string
    {
        return '<span class="status_code ' . $this->getStatusCodeCssClass($value) . '">' . $value . '</span>';
    }
}

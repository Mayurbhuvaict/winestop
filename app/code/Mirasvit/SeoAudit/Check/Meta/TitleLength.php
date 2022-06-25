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

namespace Mirasvit\SeoAudit\Check\Meta;

use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Check\AbstractCheck;

class TitleLength extends AbstractCheck
{
    public function getAllowedTypes(): array
    {
        return [UrlInterface::TYPE_PAGE];
    }

    public function isAllowedForExternal(): bool
    {
        return false;
    }

    public function getIdentifier(): string
    {
        return 'meta_title_length';
    }

    public function getImportance(): int
    {
        return 1;
    }

    public function getCheckResult(UrlInterface $url): array
    {
        $len = strlen($url->getMetaTitle());

        $msg = '';
        if ($len === 0) {
            $score = -10;
            $msg   = 'Empty meta title';
        } elseif ($len <= 20) {
            $score = 3;
            $msg   = 'Meta title too short';
        } elseif ($len < 50) {
            $score = 6;
            $msg   = 'Meta title too short';
        } elseif ($len <= 70) {
            $score = 10;
        } elseif ($len <= 90) {
            $score = 6;
            $msg   = 'Meta title too long';
        } else {
            $score = 3;
            $msg   = 'Meta title too long';
        }

        return [
            CheckResultInterface::RESULT  => $score,
            CheckResultInterface::VALUE   => $this->encodeValue($url->getMetaTitle()),
            CheckResultInterface::MESSAGE => $msg,
        ];
    }

    public function getValueType(): string
    {
        return self::VALUE_TYPE_ARRAY;
    }

    public function getLabel(): string
    {
        return "Meta Title Length";
    }

    public function getGridColumnLabel(): string
    {
        return 'Meta Title';
    }

    public function getValueGridOutput(string $value): string
    {
        return $value;
    }
}

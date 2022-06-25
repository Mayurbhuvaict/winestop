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

class DescriptionLength extends AbstractCheck
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
        return 'meta_description_length';
    }

    public function getImportance(): int
    {
        return 1;
    }

    public function getCheckResult(UrlInterface $url): array
    {
        $len = strlen($url->getMetaDescription());

        $msg = '';
        if ($len === 0) {
            $score = -10;
            $msg   = 'Empty meta description';
        } elseif ($len <= 50) {
            $score = 3;
            $msg   = 'Meta description too short';
        } elseif ($len < 110) {
            $score = 6;
            $msg   = 'Meta description too short';
        } elseif ($len <= 160) {
            $score = 10;
        } elseif ($len <= 200) {
            $score = 6;
            $msg   = 'Meta description too long';
        } else {
            $score = 3;
            $msg   = 'Meta description too long';
        }

        return [
            CheckResultInterface::RESULT  => $score,
            CheckResultInterface::VALUE   => $this->encodeValue($url->getMetaDescription()),
            CheckResultInterface::MESSAGE => $msg,
        ];
    }

    public function getValueType(): string
    {
        return self::VALUE_TYPE_STRING;
    }

    public function getLabel(): string
    {
        return "Meta Description Length";
    }

    public function getGridColumnLabel(): string
    {
        return 'Meta Description';
    }

    public function getValueGridOutput(string $value): string
    {
        return $value;
    }

}


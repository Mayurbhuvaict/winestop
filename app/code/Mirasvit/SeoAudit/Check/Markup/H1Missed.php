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


namespace Mirasvit\SeoAudit\Check\Markup;


use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Check\AbstractCheck;
use Mirasvit\SeoAudit\Repository\CheckResultRepository;

class H1Missed extends AbstractCheck
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
        return 'markup_h1_missed';
    }

    public function getImportance(): int
    {
        return 1;
    }

    public function getValueType(): string
    {
        return self::VALUE_TYPE_STRING;
    }

    public function getLabel(): string
    {
        return "H1 tag missed or empty";
    }

    public function getGridColumnLabel(): string
    {
        return "H1 tag";
    }

    public function getValueGridOutput(string $value): string
    {
        return $this->decodeValue($value);
    }

    public function getCheckResult(UrlInterface $url): array
    {
        $result = [
            CheckResultInterface::RESULT  => self::MIN_SCORE,
            CheckResultInterface::VALUE   => 'H1 tag missed',
            CheckResultInterface::MESSAGE => ''
        ];
        
        if (!$url->getContent()) {
            return $result;
        }
        
        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($url->getContent());
        libxml_clear_errors();

        $value = '';

        $headers = $dom->getElementsByTagName('h1');

        if (!$headers->count()) {
            return $result;
        }

        /** @var \DOMElement $header */
        foreach ($headers as $header) {
            $headerValue = trim($header->textContent);
            break; // check only first H1 tag
        }

        return [
            CheckResultInterface::RESULT  => $headerValue ? self::MAX_SCORE : self::MIN_SCORE,
            CheckResultInterface::VALUE   => $headerValue ?: 'H1 tag is empty',
            CheckResultInterface::MESSAGE => ''
        ];
    }

}

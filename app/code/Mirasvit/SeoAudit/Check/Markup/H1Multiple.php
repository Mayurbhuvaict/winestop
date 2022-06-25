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

class H1Multiple extends AbstractCheck
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
        return 'markup_h1_multiple';
    }

    public function getImportance(): int
    {
        return 1;
    }

    public function getValueType(): string
    {
        return self::VALUE_TYPE_ARRAY;
    }

    public function getLabel(): string
    {
        return 'Page has more then one H1 tag';
    }

    public function getGridColumnLabel(): string
    {
        return 'H1 tags';
    }

    public function getValueGridOutput(string $value): string
    {
        $value = $this->decodeValue($value);

        $output = '';

        if (!is_array($value)) {
            return $output;
        }

        foreach ($value as $header) {
            $output .= '<p>' . $header . '</p>';
        }

        return $output;
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

        $headers = $dom->getElementsByTagName('h1');

        $value = [];

        if ($headers->count()) {
            /** @var \DOMElement $header */
            foreach ($headers as $header) {
                $value[] = trim($header->textContent);
            }
        }

        return [
            CheckResultInterface::RESULT  => count($value) > 1 ? self::MIN_SCORE : self::MAX_SCORE,
            CheckResultInterface::VALUE   => $this->encodeValue($value),
            CheckResultInterface::MESSAGE => ''
        ];
    }
}

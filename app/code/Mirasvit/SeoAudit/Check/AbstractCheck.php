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


namespace Mirasvit\SeoAudit\Check;


use Mirasvit\Core\Service\SerializeService;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Repository\UrlRepoitory;
use Mirasvit\SeoAudit\Service\UrlService;

abstract class AbstractCheck
{
    const MAX_SCORE = 10;

    const MIN_SCORE = -10;

    const VALUE_TYPE_INT    = 'int';
    const VALUE_TYPE_STRING = 'string';
    const VALUE_TYPE_ARRAY  = 'array';
    const VALUE_TYPE_JSON   = 'json';

    protected $urlRepository;

    protected $urlService;

    public function __construct(
        UrlRepoitory $urlRepoitory,
        UrlService   $urlService
    ) {
        $this->urlRepository = $urlRepoitory;
        $this->urlService    = $urlService;
    }

    abstract public function getAllowedTypes(): array;
    
    abstract public function isAllowedForExternal(): bool;

    abstract public function getIdentifier(): string;

    abstract public function getImportance(): int;

    abstract public function getValueType(): string;

    abstract public function getLabel(): string;

    abstract public function getGridColumnLabel(): string;

    public function getGridFieldClass(): string
    {
        return '';
    }

    abstract public function getValueGridOutput(string $value): string;

    /**
     * @param UrlInterface $url
     *
     * @return array{result: int, message: string, value: string}
     */
    abstract public function getCheckResult(UrlInterface $url): array;

    /**
     * @param array|int|string $value
     *
     * @return string
     */
    public function encodeValue($value): string
    {
        switch ($this->getValueType()) {
            case self::VALUE_TYPE_STRING:
            case self::VALUE_TYPE_INT:
                return (string)$value;
            default:
                return SerializeService::encode($value);
        }
    }

    /**
     * @param string $value
     *
     * @return array|int|string
     */
    public function decodeValue(string $value)
    {
        switch ($this->getValueType()) {
            case self::VALUE_TYPE_INT:
                return (int)$value;
            case self::VALUE_TYPE_STRING:
                return (string)$value;
            default:
                return SerializeService::decode($value);
        }
    }

    protected function prepareLinkHtml(string $url): string
    {
        return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
    }

    public function getStatusCodeCssClass(string $code): string
    {
        $statusFirstNumber = substr($code, 0, 1);

        switch ($statusFirstNumber) {
            case 2:
                return 'success';
            case 3:
                return 'warning';
            case 4:
            case 5:
                return 'error';
            default:
                return 'notice';
        }
    }
}

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


namespace Mirasvit\SeoAudit\Check\Children;


use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Check\AbstractCheck;

class Redirect extends AbstractCheck
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
        return 'children_redirect';
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
        return 'Redirects linked to the page';
    }

    public function getGridColumnLabel(): string
    {
        return $this->getLabel();
    }

    public function getValueGridOutput(string $value): string
    {
        $ids = $this->decodeValue($value);

        $urls = $this->urlRepository->getCollection()
            ->addFieldToFilter(UrlInterface::ID, ['in' => $ids]);

        $output = '';

        foreach ($urls as $url) {
            $output .= "<p>{$this->prepareLinkHtml($url->getUrl())}</p>";
        }

        return $output;
    }

    public function getCheckResult(UrlInterface $url): array
    {
        $urls = $this->urlRepository->getCollection()
            ->addFieldToFilter(UrlInterface::PARENT_IDS, ['finset' => $url->getId()])
            ->addFieldToFilter(UrlInterface::JOB_ID, $url->getJobId())
            ->addFieldToFilter(UrlInterface::ID, ['neq' => $url->getId()])
            ->addFieldToFilter(UrlInterface::STATUS_CODE, ['like' => '3%']);

        $ids = [];

        /** @var UrlInterface $u */
        foreach ($urls as $u) {
            $ids[] = $u->getId();
        }

        $u = array_unique($ids);

        return [
            CheckResultInterface::RESULT  => count($ids) ? self::MIN_SCORE : self::MAX_SCORE,
            CheckResultInterface::VALUE   => $this->encodeValue($ids),
            CheckResultInterface::MESSAGE => ''
        ];
    }

}

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

namespace Mirasvit\Seo\Service\TemplateEngine;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Store\Model\StoreManagerInterface;

class TemplateProcessor
{
    const WIDGET_BLOCK_WRAPPER = '+++!!!________________________!!!+++';

    /**
     * @var array
     */
    private $constructions;

    /** @var Data\AbstractData[] */
    private $dataPool = [];

    private $catalogHelper;

    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        CatalogHelper $catalogHelper,
        array $dataPool
    ) {
        $this->dataPool      = $dataPool;
        $this->catalogHelper = $catalogHelper;
        $this->storeManager  = $storeManager;
    }

    /**
     * @return Data\AbstractData[]
     */
    public function getData(): array
    {
        return $this->dataPool;
    }

    /**
     * @param string $content
     * @param array  $additionalData
     *
     * @return string
     */
    public function process(string $content, array $additionalData = []): string
    {
        if (trim((string)$content) == '') {
            return $content;
        }

        $emptyValue = "EMPTY";

        $content = $this->parseWidget($content);
        $vars    = $this->getVars($content);

        # iteration 1: replace variables {...} [...]
        foreach ($vars as $placeholder => $var) {
            if (strpos($var, '_') === false) {
                $var = "global_$var";
            }

            list($objectCode, $attributeCode) = explode('_', $var, 2);

            $value = null;
            if (isset($this->dataPool[$objectCode])) {
                $value = (string)$this->dataPool[$objectCode]->getValue($attributeCode, $additionalData);
                $value = trim($value);
            }

            if (!$value && $value !== '0') {
                $value = $emptyValue;
            } else {
                $value = $this->applyFilters($value);
            }

            $content = str_replace($placeholder, $value, $content);
        }

        # iteration 2: replace [..{...}.] with empty value
        $vars = $this->getVars($content);

        foreach ($vars as $placeholder => $var) {
            if (strpos($var, $emptyValue) !== false) {
                $content = str_replace($placeholder, '', $content);
            } else {
                $content = str_replace($placeholder, $var, $content);
            }
        }

        # iteration 3: remove EMPTY placeholder
        $content = str_replace($emptyValue, '', $content);

        $content = $this->restoreWidgetBlocks($content);

        return $content;
    }

    private function getVars(string $content): array
    {
        $bAOpen  = '[ZZZZZ';
        $bAClose = 'ZZZZZ]';
        $bBOpen  = '{WWWWW';
        $bBClose = 'WWWWW}';

        #prevent error, if string contains Z or W
        $content = str_replace('Z', '¤', $content);
        $content = str_replace('W', '¶', $content);

        $content = str_replace('[', $bAOpen, $content);
        $content = str_replace(']', $bAClose, $content);
        $content = str_replace('{', $bBOpen, $content);
        $content = str_replace('}', $bBClose, $content);

        $pattern = '/(\{|\[)+(Z{5}([^ZW])+Z{5}|W{5}([^ZW])+W{5})*(\}|\])+/';#deepest variable

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $vars = [];
        foreach ($matches as $match) {
            $var         = str_replace([$bAOpen, $bAClose, $bBOpen, $bBClose], '', $match[0]);
            $placeholder = $match[0];

            $placeholder = str_replace($bAOpen, '[', $placeholder);
            $placeholder = str_replace($bAClose, ']', $placeholder);
            $placeholder = str_replace($bBOpen, '{', $placeholder);
            $placeholder = str_replace($bBClose, '}', $placeholder);

            $var = str_replace('¤', 'Z', $var);
            $var = str_replace('¶', 'W', $var);

            $placeholder = str_replace('¤', 'Z', $placeholder);
            $placeholder = str_replace('¶', 'W', $placeholder);

            $vars[$placeholder] = $var;
        }

        return $vars;
    }

    private function parseWidget(string $content): string
    {
        if (preg_match_all(
            \Magento\Framework\Filter\Template::CONSTRUCTION_PATTERN,
            $content,
            $constructions,
            PREG_SET_ORDER
        )) {
            $this->constructions = $constructions;
            foreach ($constructions as $key => $construction) {
                $content = str_replace(
                    $construction[0],
                    self::WIDGET_BLOCK_WRAPPER . $key . self::WIDGET_BLOCK_WRAPPER,
                    $content
                );
            }
        }

        return $content;
    }

    private function restoreWidgetBlocks(string $content): string
    {
        if ($this->constructions) {
            foreach ($this->constructions as $key => $construction) {
                $content = str_replace(
                    self::WIDGET_BLOCK_WRAPPER . $key . self::WIDGET_BLOCK_WRAPPER,
                    $construction[0],
                    $content
                );
            }
            $this->constructions = null;
        }

        return $content;
    }

    private function applyFilters(string $content): string
    {
        return $this->catalogHelper->getPageTemplateProcessor()->filter($content);
    }
}

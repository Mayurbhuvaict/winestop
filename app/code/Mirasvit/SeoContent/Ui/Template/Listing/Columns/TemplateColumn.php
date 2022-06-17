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



namespace Mirasvit\SeoContent\Ui\Template\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;

class TemplateColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getName()] = $this->renderTemplate($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $template
     * @return string
     */
    private function renderTemplate(array $template)
    {
        $html = '<div class="mst-seo-content__template-listing-column-template">';

        $values = [
            TemplateInterface::META_TITLE           => __('Meta Title'),
            TemplateInterface::META_KEYWORDS        => __('Meta Keywords'),
            TemplateInterface::META_DESCRIPTION     => __('Meta Description'),
            TemplateInterface::TITLE                => __('Title (H1)'),
            TemplateInterface::DESCRIPTION          => __('SEO Description'),
            TemplateInterface::SHORT_DESCRIPTION    => __('Product Short Description'),
            TemplateInterface::FULL_DESCRIPTION     => __('Product Description'),
            TemplateInterface::CATEGORY_DESCRIPTION => __('Category Description'),
        ];

        foreach ($values as $key => $label) {
            $value = (string)$template[$key];
            if (!trim($value)) {
                continue;
            }

            $value = $this->highlightTags($value);
            $html .= "<p><u>$label:</u> $value</p>";
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * @param string $string
     * @return string
     */
    public function highlightTags($string)
    {
        $string = preg_replace('/([{\[][a-zA-Z_|]*[}\]])/', "<i>$1</i>", $string);
        //$string = preg_replace('/(\[[\w\W]*\])/', "<span>$1</span>", $string);

        return $string;
    }
}

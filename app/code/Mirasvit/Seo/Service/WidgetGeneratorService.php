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



namespace Mirasvit\Seo\Service;

use Mirasvit\Seo\Api\Service\WidgetGeneratorServiceInterface;
use Magento\Widget\Model\Template\Filter as WidgetTemplateFilter;

/**
 * Widget Generator
 */
class WidgetGeneratorService implements WidgetGeneratorServiceInterface
{
    /**
     * @var WidgetTemplateFilter
     */
    private $widgetFilter;

    /**
     * WidgetGeneratorService constructor.
     * @param WidgetTemplateFilter $widgetFilter
     */
    public function __construct(
        WidgetTemplateFilter $widgetFilter
    ) {
        $this->widgetFilter = $widgetFilter;
    }

    /**
     * Replace widget ({{widget name=...}}) in string
     *
     * {@inheritdoc}
     */
    public function generateWidget($string)
    {
        if (preg_match_all(
            \Magento\Framework\Filter\Template::CONSTRUCTION_PATTERN,
            $string,
            $constructions,
            PREG_SET_ORDER
        )
        ) {
            foreach ($constructions as $construction) {
                $html = $this->widgetFilter->generateWidget($construction);
                if ($html) {
                    $string = str_replace($construction[0], $html, $string);
                }
            }
        }

        return $string;
    }
}

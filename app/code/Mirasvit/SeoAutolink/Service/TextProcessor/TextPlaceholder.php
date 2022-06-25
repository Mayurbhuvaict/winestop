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



namespace Mirasvit\SeoAutolink\Service\TextProcessor;

class TextPlaceholder
{
    /**
     * @var array
     */
    private $translationTableArray = [];

    /**
     * @var string|string[]|null
     */
    private $tokenizedText;

    /**
     * @param string $text
     * @param array  $patterns
     */
    public function __construct($text, $patterns)
    {
        $patterns[] = '#<[^>]+>#iU'; // generic html tag , must be placed last
        $this->tokenizedText = preg_replace_callback($patterns, [$this, 'placeholder'], $text);
    }

    /**
     * @return array
     */
    public function getTranslationTableArray()
    {
        return $this->translationTableArray;
    }

    /**
     * @return string
     */
    public function getTokenizedText()
    {
        return $this->tokenizedText;
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    public function placeholder($matches)
    {
        $sequence = count($this->translationTableArray);

        $placeholder = ' xkjndsfkjnakcx' . $sequence . 'cxmkmweof329jc ';

        $this->translationTableArray[$placeholder] = $matches[0];

        return $placeholder;
    }
}

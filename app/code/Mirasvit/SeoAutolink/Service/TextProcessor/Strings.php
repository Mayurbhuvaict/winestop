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

class Strings
{
    /**
     * Replace special chars in text to their altenatives
     *
     * @param string $source
     *
     * @return string
     */
    public static function replaceSpecialCharacters($source)
    {
        // substitute some special html characters with their 'real' value
        $searchTamp = [
            '&amp;Eacute;',
            '&amp;Euml;',
            '&amp;Oacute;',
            '&amp;eacute;',
            '&amp;euml;',
            '&amp;oacute;',
            '&amp;Agrave;',
            '&amp;Egrave;',
            '&amp;Igrave;',
            '&amp;Iacute;',
            '&amp;Iuml;',
            '&amp;Ograve;',
            '&amp;Ugrave;',
            '&amp;agrave;',
            '&amp;egrave;',
            '&amp;igrave;',
            '&amp;iacute;',
            '&amp;iuml;',
            '&amp;ograve;',
            '&amp;ugrave;',
            '&amp;Ccedil;',
            '&amp;ccedil;',
            '&amp;ecirc;',
            '&amp;auml;',
            '&amp;uuml;'
        ];

        $replaceTamp = [
            'É',
            'Ë',
            'Ó',
            'é',
            'ë',
            'ó',
            'À',
            'È',
            'Ì',
            'Í',
            'Ï',
            'Ò',
            'Ù',
            'à',
            'è',
            'ì',
            'í',
            'ï',
            'ò',
            'ù',
            'Ç',
            'ç',
            'ê',
            'ä',
            'ü'
        ];

        $searchT = [
            '&Eacute;',
            '&Euml;',
            '&Oacute;',
            '&eacute;',
            '&euml;',
            '&oacute;',
            '&Agrave;',
            '&Egrave;',
            '&Igrave;',
            '&Iacute;',
            '&Iuml;',
            '&Ograve;',
            '&Ugrave;',
            '&agrave;',
            '&egrave;',
            '&igrave;',
            '&iacute;',
            '&iuml;',
            '&ograve;',
            '&ugrave;',
            '&Ccedil;',
            '&ccedil;',
            '&auml;',
            '&uuml;'
        ];

        $replaceT = [
            'É',
            'Ë',
            'Ó',
            'é',
            'ë',
            'ó',
            'À',
            'È',
            'Ì',
            'Í',
            'Ï',
            'Ò',
            'Ù',
            'à',
            'è',
            'ì',
            'í',
            'ï',
            'ò',
            'ù',
            'Ç',
            'ç',
            'ä',
            'ü'
        ];

        $source = str_replace($searchTamp, $replaceTamp, $source);
        $source = str_replace($searchT, $replaceT, $source);

        return $source;
    }

    /**
     * Split text to array to create the sql query
     *
     * @param string $text
     *
     * @return array
     */
    public static function splitText($text)
    {
        $maxTextSymbols    = 1000; //number of characters for split the text
        $numberReturnWords = 5;      //number of words which will in every part of the split text
        $textSymbolsCount  = strlen($text);
        if ($textSymbolsCount > $maxTextSymbols) {
            $selectNumber = ceil($textSymbolsCount / $maxTextSymbols);
        }

        $textArrayWithMaxSymbols = [];
        if (isset($selectNumber)) {
            $textArray = str_split($text, $maxTextSymbols);
            foreach ($textArray as $textKey => $textVal) {
                if ($textKey == 0) {
                    $keyBefore                         = $textKey;
                    $textArrayWithMaxSymbols[$textKey] = $textVal;
                } else {
                    $currentText = explode(' ', $textVal, $numberReturnWords);
                    if (count($currentText) == $numberReturnWords) {
                        $currentTextShift = $currentText;
                        array_shift($currentTextShift);
                        $textArrayWithMaxSymbols[$textKey] = implode(' ', $currentTextShift);
                        $currentTextPop                    = $currentText;
                        array_pop($currentTextPop);
                        $textArrayWithMaxSymbols[$keyBefore] .= implode(' ', $currentTextPop);
                        $keyBefore                           = $textKey;
                    } else {
                        $textArrayWithMaxSymbols[$textKey] = implode(' ', $currentText);
                    }
                }
            }
        }

        if (empty($textArrayWithMaxSymbols)) {
            $textArrayWithMaxSymbols[] = $text;
        }

        return $textArrayWithMaxSymbols;
    }

    /**
     * @param string $keyword
     * @param int    $start
     * @param int    $len
     *
     * @return string
     */
    public static function substr($keyword, $start = 0, $len = 1)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($keyword, $start, $len);
        }

        return substr($keyword, $start, $len);
    }
}

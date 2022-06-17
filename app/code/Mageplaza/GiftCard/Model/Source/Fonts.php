<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use TCPDF;

/**
 * Class Fonts
 * @package Mageplaza\GiftCard\Model\Source
 */
class Fonts extends TCPDF implements ArrayInterface
{
    const ROBOTO                  = 'Roboto';
    const OPEN_SANS               = 'Open Sans';
    const LATO                    = 'Lato';
    const MONTSERRAT              = 'Montserrat';
    const ROBOTO_CONDENSED        = 'Roboto Condensed';
    const SOURCE_SANS_PRO         = 'Source Sans Pro';
    const OSWALD                  = 'Oswald';
    const RALEWAY                 = 'Raleway';
    const SLABO_27PX              = 'Slabo 27px';
    const PT_SANS                 = 'PT Sans';
    const MERRIWEATHER            = 'Merriweather';
    const ROBOTO_SLAB             = 'Roboto Slab';
    const OPEN_SANS_CONDENSED_300 = 'Open Sans Condensed';
    const UBUNTU                  = 'Ubuntu';
    const NOTO_SANS               = 'Noto Sans';
    const POPPINS                 = 'Poppins';
    const ROBOTO_MONO             = 'Roboto Mono';
    const PLAYFAIR_DISPLAY        = 'Playfair Display';
    const LORA                    = 'Lora';
    const PT_SERIF                = 'PT Serif';
    const TITILLIUM_WEB           = 'Titillium Web';
    const MULI                    = 'Muli';
    const ARIMO                   = 'Arimo';
    const FIRA_SANS               = 'Fira Sans';
    const PT_SANS_NARROW          = 'PT Sans Narrow';
    const NUNITO                  = 'Nunito';
    const NOTO_SERIF              = 'Noto Serif';
    const INCONSOLATA             = 'Inconsolata';
    const NANUM_GOTHIC            = 'Nanum Gothic';
    const CRIMSON_TEXT            = 'Crimson Text';

    /**
     * @return array
     */
    public function getGoogleFonts()
    {
        return [
            [
                'value' => self::ROBOTO,
                'label' => __('Roboto')
            ],
            [
                'value' => self::OPEN_SANS,
                'label' => __('Open+Sans')
            ],
            [
                'value' => self::LATO,
                'label' => __('Lato')
            ],
            [
                'value' => self::MONTSERRAT,
                'label' => __('Montserrat')
            ],
            [
                'value' => self::ROBOTO_CONDENSED,
                'label' => __('Roboto+Condensed')
            ],
            [
                'value' => self::SOURCE_SANS_PRO,
                'label' => __('Source+Sans+Pro')
            ],
            [
                'value' => self::OSWALD,
                'label' => __('Oswald')
            ],
            [
                'value' => self::RALEWAY,
                'label' => __('Raleway')
            ],
            [
                'value' => self::SLABO_27PX,
                'label' => __('Slabo+27px')
            ],
            [
                'value' => self::PT_SANS,
                'label' => __('PT+Sans')
            ],
            [
                'value' => self::MERRIWEATHER,
                'label' => __('Merriweather')
            ],
            [
                'value' => self::ROBOTO_SLAB,
                'label' => __('Roboto+Slab')
            ],
            [
                'value' => self::OPEN_SANS_CONDENSED_300,
                'label' => __('Open+Sans+Condensed:300')
            ],
            [
                'value' => self::UBUNTU,
                'label' => __('Ubuntu')
            ],
            [
                'value' => self::NOTO_SANS,
                'label' => __('Noto+Sans')
            ],
            [
                'value' => self::POPPINS,
                'label' => __('Poppins')
            ],
            [
                'value' => self::ROBOTO_MONO,
                'label' => __('Roboto+Mono')
            ],
            [
                'value' => self::PLAYFAIR_DISPLAY,
                'label' => __('Playfair+Display')
            ],
            [
                'value' => self::LORA,
                'label' => __('Lora')
            ],
            [
                'value' => self::PT_SERIF,
                'label' => __('PT+Serif')
            ],
            [
                'value' => self::TITILLIUM_WEB,
                'label' => __('Titillium+Web')
            ],
            [
                'value' => self::MULI,
                'label' => __('Muli')
            ],
            [
                'value' => self::ARIMO,
                'label' => __('Arimo')
            ],
            [
                'value' => self::FIRA_SANS,
                'label' => __('Fira+Sans')
            ],
            [
                'value' => self::PT_SANS_NARROW,
                'label' => __('PT+Sans+Narrow')
            ],
            [
                'value' => self::NUNITO,
                'label' => __('Nunito')
            ],
            [
                'value' => self::NOTO_SERIF,
                'label' => __('Noto+Serif')
            ],
            [
                'value' => self::INCONSOLATA,
                'label' => __('Inconsolata')
            ],
            [
                'value' => self::NANUM_GOTHIC,
                'label' => __('Nanum+Gothic')
            ],
            [
                'value' => self::CRIMSON_TEXT,
                'label' => __('Crimson+Text')
            ],
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $fonts = [
            ['value' => 'times', 'label' => 'Times-Roman'],
            ['value' => 'helvetica', 'label' => 'Helvetica'],
            ['value' => 'courier', 'label' => 'Courier']
        ];

        $result = array_merge($fonts, $this->getGoogleFonts());

        usort($result, function ($a, $b) {
            return ($a['label'] <= $b['label']) ? -1 : 1;
        });

        return $result;
    }
}

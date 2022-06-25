<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2021 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     MIT
 */

namespace Snk\Captcha\Model\Config;

use Snk\Captcha\Model\MathCaptcha;

class Type implements \Magento\Framework\Data\OptionSourceInterface
{
    private $types = [
        'default'         => 'Default',
        MathCaptcha::TYPE => 'Math',
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->types as $code => $name) {
            $optionArray[] = ['label' => __($name), 'value' => $code];
        }
        return $optionArray;
    }
}

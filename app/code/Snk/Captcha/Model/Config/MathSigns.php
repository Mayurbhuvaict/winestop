<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2021 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     MIT
 */

namespace Snk\Captcha\Model\Config;

use Snk\Captcha\Model\ExpressionGenerator;

class MathSigns implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach (ExpressionGenerator::AVAILABLE_SIGNS as $sign) {
            $optionArray[] = ['label' => $sign, 'value' => $sign];
        }
        return $optionArray;
    }
}

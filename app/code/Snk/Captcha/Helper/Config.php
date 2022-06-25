<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2021 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     MIT
 */

namespace Snk\Captcha\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getExpressionType()
    {
        return $this->config->getValue('customer/captcha/expression_type', ScopeInterface::SCOPE_STORES);
    }

    /**
     * @return string[]
     */
    public function getAllowedMathSigns()
    {
        $value = $this->config->getValue('customer/captcha/allowed_math_signs', ScopeInterface::SCOPE_STORES);
        return explode(',', $value);
    }

    /**
     * @return string
     */
    public function getLabelText()
    {
        return $this->config->getValue('customer/captcha/label_text', ScopeInterface::SCOPE_STORES);
    }
}

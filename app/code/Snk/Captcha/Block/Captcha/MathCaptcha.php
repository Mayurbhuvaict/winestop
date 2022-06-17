<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2021 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     MIT
 */
namespace Snk\Captcha\Block\Captcha;

use Magento\Captcha\Block\Captcha\DefaultCaptcha;
use Magento\Captcha\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Snk\Captcha\Helper\Config;

/**
 * Captcha block
 * TODO: use view model instead
 */
class MathCaptcha extends DefaultCaptcha
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        Data $captchaData,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $captchaData, $data);
        $this->config = $config;
    }

    /**
     * @var string
     */
    protected $_template = 'Snk_Captcha::math.phtml';

    /**
     * @return string
     */
    public function getLabelText()
    {
        return $this->config->getLabelText();
    }
}

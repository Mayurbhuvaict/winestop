<?php

namespace Winestop\Custom\Block;

class DefaultCaptcha extends \Magento\Captcha\Block\Captcha\DefaultCaptcha
{
	/**
     * @var string
     */
    protected $_template = 'Winestop_Custom::default.phtml';
}
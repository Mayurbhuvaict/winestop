<?php
/**
 *
 * Copyright © 2018 iCreative Technologies. All rights reserved.
 *
 * @company :   iCreative Technologies
 * @package :   Ict_Stateshipping
 * @copyright :   Copyright (c) 2018 iCreative Technologies (http://icreativetechnologies.com/)
 */

namespace Ict\Stateshipping\Block;

class Stateshipping extends \Magento\Framework\View\Element\Template
{
    public function getConfig($value)
    {
        return $this->_scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}

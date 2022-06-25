<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Model;

/**
 * Function modem
 */
class Attempts extends \Magento\Framework\Model\AbstractModel
{

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Wyomind\Watchlog\Model\ResourceModel\Attempts');
    }
}

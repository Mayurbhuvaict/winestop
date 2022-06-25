<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Model\ResourceModel;

/**
 * Function resource
 */
class Attempts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * internal constructor
     */
    protected function _construct()
    {
        $this->_init('watchlog', 'id');
    }
}

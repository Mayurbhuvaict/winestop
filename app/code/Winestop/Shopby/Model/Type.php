<?php

namespace Winestop\Shopby\Model;

use Magento\Framework\Model\AbstractModel;

class Type extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Winestop\Shopby\Model\ResourceModel\Type');
    }
}
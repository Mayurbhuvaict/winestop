<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Model\ResourceModel\Store;

class Collection extends \Magento\Store\Model\ResourceModel\Store\Collection
{
    public function getFirstStoreId()
    {
        $this->getSelect()->limit(1);
        return $this->getFirstItem()->getStoreId();
    }
}

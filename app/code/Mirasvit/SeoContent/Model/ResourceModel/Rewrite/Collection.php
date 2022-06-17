<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Model\ResourceModel\Rewrite;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\SeoContent\Model\Rewrite::class,
            \Mirasvit\SeoContent\Model\ResourceModel\Rewrite::class
        );
    }

    /**
     * @param object|int $store
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if ($store instanceof Store) {
            $id = $store->getId();
        } else {
            $id = $store;
        }

        $this->getSelect()->where('FIND_IN_SET(' . $id . ', store_ids) OR store_ids = 0');

        return $this;
    }
}

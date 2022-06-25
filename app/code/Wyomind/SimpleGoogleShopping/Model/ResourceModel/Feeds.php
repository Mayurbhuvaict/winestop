<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Model\ResourceModel;

/**
 * Simple google shopping data feed mysql resource
 */
class Feeds extends \Wyomind\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected $module = 'SimpleGoogleShopping';

    /**
     * {@inheritdoc}
     */
    protected $entity = 'Feeds';

    /**
     * {@inheritdoc}
     */
    protected $fieldsNotToCheck = [
        'simplegoogleshopping_time',
        'simplegoogleshopping_report',
        'simplegoogleshopping_promotions_report'
    ];

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('simplegoogleshopping_feeds', 'simplegoogleshopping_id');
    }
}

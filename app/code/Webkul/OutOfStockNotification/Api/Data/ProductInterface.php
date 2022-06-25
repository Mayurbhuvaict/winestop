<?php
/**
 * Webkul Software
 *
 * @category    Webkul
 * @package     Webkul_OutOfStockNotification
 * @author      Webkul
 * @copyright   Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license     https://store.webkul.com/license.html
 */
namespace Webkul\OutOfStockNotification\Api\Data;

/**
 * OutOfStockNotification Product interface.
 *
 * @api
 */
interface ProductInterface
{
   
    /**
    * Constants for keys of data array. Identical to the name of the getter in snake case
    */
    const ENTITY_ID    = 'id';
    /***/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param \Webkul\OutOfStockNotification\Api\Data\ProductInterface
     * @return int $id
     */
    public function setId($id);
}

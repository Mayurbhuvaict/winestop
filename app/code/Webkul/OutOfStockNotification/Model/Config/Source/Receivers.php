<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_OutOfStockNotification
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\OutOfStockNotification\Model\Config\Source;

/**
 * Effect Class of Model Config Source
 */
class Receivers
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data =  [
                    ['value'=>'1', 'label'=>__('ALL Customers')],
                    ['value'=>'0', 'label'=>__('Subscribers')]
        ];
        return $data;
    }
}

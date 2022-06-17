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
class Effect
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data =  [
                    ['value'=>'1', 'label'=>__('Auto')],
                    ['value'=>'0', 'label'=>__('Manual')]
        ];
        return $data;
    }
}

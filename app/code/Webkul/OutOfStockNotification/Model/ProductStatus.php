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
namespace Webkul\OutOfStockNotification\Model;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * ProductStatus Class
 */
class ProductStatus implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
                        [
                            'label' => 'Pending',
                            'value' => 0
                        ],
                        [
                            'label' => 'Notified',
                            'value' => 1
                        ]
                    ];
        return $options;
    }
}

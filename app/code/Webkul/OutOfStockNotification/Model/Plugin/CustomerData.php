<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_OutOfStockNotification
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\OutOfStockNotification\Model\Plugin;

use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * CustomerData Plugin Class
 */
class CustomerData
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * Construct function for Plugin Class
     *
     * @param CurrentCustomer $currentCustomer currentCustomer
     *
     * @return void
     */
    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Function to set customer Email in section data as additional data
     *
     * @param \Magento\Customer\CustomerData\Customer $subject subject
     * @param object                                  $result  result
     *
     * @return object
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        if ($this->currentCustomer->getCustomerId()) {
            $customer = $this->currentCustomer->getCustomer();
            $result['email'] = $customer->getEmail();
        }
        return $result;
    }
}

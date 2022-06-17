<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */


































































































































































































namespace Aheadworks\AdvancedReports\Model\Filter\Dashboard;

use Magento\Customer\Model\GroupManagement;

/**
 * Class CustomerGroup
 *
 * @package Aheadworks\AdvancedReports\Model\Filter\Dashboard
 */
class CustomerGroup extends \Aheadworks\AdvancedReports\Model\Filter\CustomerGroup
{
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $customerGroupId = $this->request->getParam(self::REQUEST_PARAM);
        if (null !== $customerGroupId && '' !== $customerGroupId) {
            return $customerGroupId;
        }

        return $this->getDefaultValue();
    }
}

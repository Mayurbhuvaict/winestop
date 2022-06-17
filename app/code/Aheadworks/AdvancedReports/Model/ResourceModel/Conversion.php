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


















































































namespace Aheadworks\AdvancedReports\Model\ResourceModel;

/**
 * Class Conversion
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel
 */
class Conversion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Status of view rows in index
     *
     * @var string
     */
    const VIEWED_STATUS = 'viewed';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_conversion', 'id');
    }
}

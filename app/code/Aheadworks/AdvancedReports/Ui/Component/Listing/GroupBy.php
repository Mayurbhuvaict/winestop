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























































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing;

use Aheadworks\AdvancedReports\Ui\Component\OptionsContainer;

/**
 * Class GroupBy
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing
 */
class GroupBy extends OptionsContainer
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $groupByFilter = $this->context->getDataProvider()->getDefaultFilterPool()->getFilter('group_by');
        $config = $this->getData('config');

        $config['currentValue'] = $groupByFilter->getValue();

        $this->setData('config', $config);
    }
}

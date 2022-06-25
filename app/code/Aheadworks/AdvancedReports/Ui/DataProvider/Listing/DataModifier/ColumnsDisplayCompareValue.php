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




































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifier;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifierInterface;

/**
 * Class ColumnsDisplayCompareValue
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifier
 */
class ColumnsDisplayCompareValue extends AbstractDataModifier implements DataModifierInterface
{
    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareComponentData(UiComponentInterface $component)
    {
        $columns = $this->componentInspector->getColumns($component);
        foreach ($columns as $column) {
            $config = $column->getData('config');
            $config['displayCompareValue'] = false;
            $column->setData('config', $config);
        }
    }
}

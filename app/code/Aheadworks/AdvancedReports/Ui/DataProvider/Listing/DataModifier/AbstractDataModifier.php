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

use Aheadworks\AdvancedReports\Ui\Component\Inspector as ComponentInspector;
use Magento\Framework\View\Element\UiComponentInterface;
use Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifierInterface;

/**
 * Class AbstractDataModifier
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifier
 */
abstract class AbstractDataModifier implements DataModifierInterface
{
    /**
     * @var ComponentInspector
     */
    protected $componentInspector;

    /**
     * @param ComponentInspector $componentInspector
     */
    public function __construct(
        ComponentInspector $componentInspector
    ) {
        $this->componentInspector = $componentInspector;
    }

    /**
     * @inheritdoc
     */
    public function prepareSourceData($data, UiComponentInterface $component)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareComponentData(UiComponentInterface $component)
    {
        return true;
    }
}

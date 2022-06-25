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

































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Listing;

use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class DataModifierComposite
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Listing
 */
class DataModifierComposite implements DataModifierInterface
{
    /**
     * @var DataModifierInterface[]
     */
    private $dataModifiers;

    /**
     * @param DataModifierInterface[] $dataModifiers
     */
    public function __construct(
        array $dataModifiers
    ) {
        $this->dataModifiers = $dataModifiers;
    }

    /**
     * @inheritdoc
     */
    public function prepareSourceData($data, UiComponentInterface $component)
    {
        foreach ($this->dataModifiers as $dataModifier) {
            $data = $dataModifier->prepareSourceData($data, $component);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareComponentData(UiComponentInterface $component)
    {
        foreach ($this->dataModifiers as $dataModifier) {
            $dataModifier->prepareComponentData($component);
        }
    }
}

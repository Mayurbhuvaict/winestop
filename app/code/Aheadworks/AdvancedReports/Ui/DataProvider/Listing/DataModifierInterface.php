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
 * Interface DataModifierInterface
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Listing
 */
interface DataModifierInterface
{
    /**
     * Prepare source data for listing
     *
     * @param array $data
     * @param UiComponentInterface $component
     * @return array
     */
    public function prepareSourceData($data, UiComponentInterface $component);

    /**
     * Prepare component data
     *
     * @param UiComponentInterface $component
     * @return mixed
     */
    public function prepareComponentData(UiComponentInterface $component);
}

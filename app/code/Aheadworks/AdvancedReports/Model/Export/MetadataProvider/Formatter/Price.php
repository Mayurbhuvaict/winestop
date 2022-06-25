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

















































































































































































namespace Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Price
 *
 * @package Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter
 */
class Price implements FormatterInterface
{
    /**
     * Formatter type
     */
    const TYPE = 'price';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceFormatter;

    /**
     * @param Filter $filter
     * @param PriceCurrencyInterface $priceFormatter
     */
    public function __construct(
        Filter $filter,
        PriceCurrencyInterface $priceFormatter
    ) {
        $this->filter = $filter;
        $this->priceFormatter = $priceFormatter;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function format($field, $value)
    {
        return $this->priceFormatter->format(
            $value,
            false,
            null,
            null,
            $this->getCurrencyCode()
        );
    }

    /**
     * Retrieve currency code
     *
     * @return string
     * @throws LocalizedException
     */
    private function getCurrencyCode()
    {
        $component = $this->filter->getComponent();
        $dataProvider = $component->getContext()->getDataProvider();
        return $dataProvider->getCurrencyCode();
    }
}

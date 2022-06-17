<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Pricing\Render;

use Exception;
use Magento\Catalog\Pricing\Render\FinalPriceBox as CatalogRender;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\GiftCard\Helper\Data;

/**
 * Class for final_price rendering
 */
class FinalPriceBox extends CatalogRender
{
    /**
     * @var AmountFactory
     */
    private $amountFactory;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Option amounts
     *
     * @var array
     */
    protected $_optionAmounts = [];

    /**
     * Min max values
     *
     * @var array
     */
    protected $_minMax = [];

    /**
     * FinalPriceBox constructor.
     *
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param AmountFactory $amountFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        AmountFactory $amountFactory,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->amountFactory = $amountFactory;
        $this->priceCurrency = $priceCurrency;

        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $data
        );
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->findMinMaxValue();
    }

    /**
     * @return AmountInterface
     */
    public function getMinimalPrice()
    {
        $minimalPrice = $this->priceCurrency->convert($this->_minMax['min']);

        return $this->amountFactory->create($minimalPrice);
    }

    /**
     * @return AmountInterface
     */
    public function getMaximalPrice()
    {
        $maximalPrice = $this->priceCurrency->convert($this->_minMax['max']);

        return $this->amountFactory->create($maximalPrice);
    }

    /**
     * @return bool
     */
    public function isFixedPrice()
    {
        return !$this->isRangeAvailable() && (count($this->getOptionPrices()) === 1);
    }

    /**
     * @return bool
     */
    public function isRangeAvailable()
    {
        return $this->saleableItem->getAllowAmountRange();
    }

    /**
     * @return array
     */
    public function getOptionPrices()
    {
        if (empty($this->_optionAmounts)) {
            try {
                $amountJson = $this->saleableItem->getGiftCardAmounts() ?: [];
                $amounts    = is_string($amountJson) ? Data::jsonDecode($amountJson) : $amountJson;
            } catch (Exception $e) {
                $amounts = [];
            }

            $this->_optionAmounts = [];
            foreach ($amounts as $amount) {
                $this->_optionAmounts[] = $amount['price'];
            }
        }

        return $this->_optionAmounts;
    }

    /**
     * @return $this
     */
    protected function findMinMaxValue()
    {
        $max = null;
        $min = $max;
        if ($this->isRangeAvailable()) {
            $rate = $this->saleableItem->getPriceRate() / 100;
            $min  = ($this->saleableItem->getMinAmount() ?: 0) * $rate;
            $max  = ($this->saleableItem->getMaxAmount() ?: 0) * $rate;
        }

        $maxOp        = null;
        $minOp        = $maxOp;
        $optionPrices = $this->getOptionPrices();
        if (count($optionPrices)) {
            $minOp = min($optionPrices);
            $maxOp = max($optionPrices);
        }

        $this->_minMax = [
            'min' => $min === null ? $minOp : ($minOp === null ? $min : min($min, $minOp)),
            'max' => $max === null ? $maxOp : ($maxOp === null ? $max : max($max, $maxOp))
        ];

        return $this;
    }
}

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

namespace Mageplaza\GiftCard\Block\Sales\Item;

use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer;
use Magento\Sales\Model\Order\Item;
use Mageplaza\GiftCard\Helper\Product;

/**
 * Class Renderer
 * @package Mageplaza\GiftCard\Block\Sales\Item
 */
class Renderer extends DefaultRenderer
{
    /**
     * @var Product
     */
    private $helper;

    /**
     * Renderer constructor.
     *
     * @param Context $context
     * @param StringUtils $string
     * @param OptionFactory $productOptionFactory
     * @param Product $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        OptionFactory $productOptionFactory,
        Product $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $string, $productOptionFactory, $data);
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getItemOptions()
    {
        /** @var Item $item */
        $item = $this->getOrderItem();

        $itemOptions = $this->helper->getOptionList($item, parent::getItemOptions());

        $totalCodes = $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
        if ($totalCodes) {
            $giftCardCodes = $this->helper->getGiftCardCodesFromIds($item->getProductOptionByCode('giftcards') ?: []);
            for ($i = count($giftCardCodes); $i < $totalCodes; $i++) {
                $giftCardCodes[] = __('N/A');
            }

            $itemOptions[] = [
                'label'       => __('Gift Codes'),
                'value'       => implode('<br />', $giftCardCodes),
                'custom_view' => true,
            ];
        }

        return $itemOptions;
    }
}

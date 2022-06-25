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

namespace Mageplaza\GiftCard\Block\Adminhtml\Order\Items;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Items\Column\Name;
use Mageplaza\GiftCard\Helper\Product;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\GiftCardFactory;

/**
 * Class Giftcard
 * @package Mageplaza\GiftCard\Block\Adminhtml\Order\Items
 */
class Giftcard extends Name
{
    /**
     * @var Product
     */
    protected $_gcHelper;

    /**
     * @var GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * Giftcard constructor.
     *
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Registry $registry
     * @param OptionFactory $optionFactory
     * @param Product $gcProductHelper
     * @param GiftCardFactory $giftCardFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry $registry,
        OptionFactory $optionFactory,
        Product $gcProductHelper,
        GiftCardFactory $giftCardFactory,
        array $data = []
    ) {
        $this->_gcHelper        = $gcProductHelper;
        $this->_giftCardFactory = $giftCardFactory;

        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getOrderOptions()
    {
        $item = $this->getItem();

        $giftCardOptions = $this->_gcHelper->getOptionList(
            $item,
            parent::getOrderOptions(),
            $this->getOrder()->getStoreId()
        );

        $totalCodes = $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
        if ($totalCodes) {
            $giftCardCodes = [];
            $giftCards     = $this->_giftCardFactory->create()->getCollection()
                ->addFieldToFilter(
                    'giftcard_id',
                    ['in' => $item->getProductOptionByCode('giftcards')]
                );
            foreach ($giftCards->getItems() as $giftCard) {
                $code = $giftCard->getCode();
                if ($this->getRequest()->getFullActionName() === 'sales_order_creditmemo_new') {
                    if ((int) $giftCard->getStatus() === Status::STATUS_CANCELLED) {
                        $code .= __(' (Cancelled)');
                    } elseif ($giftCard->getInitBalance() !== $giftCard->getBalance()) {
                        $code .= __(' (Used)');
                    }
                }

                $giftCardCodes[] = $code;
            }
            for ($i = count($giftCardCodes); $i < $totalCodes; $i++) {
                $giftCardCodes[] = __('N/A');
            }
            $giftCardOptions[] = [
                'label'       => __('Gift Codes'),
                'value'       => implode('<br />', $giftCardCodes),
                'custom_view' => true,
            ];
        }

        return $giftCardOptions;
    }
}

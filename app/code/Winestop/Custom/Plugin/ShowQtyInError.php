<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Winestop\Custom\Plugin;

use Magento\CatalogInventory\Api\Data\StockItemInterface;

class ShowQtyInError
{
    protected $redirectinterface;

    protected $requestinterface;

    protected $cart;

    public function __construct(
        \Magento\Framework\App\RequestInterface $requestinterface,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\App\Response\RedirectInterface $redirectinterface
    ) {
        $this->redirectinterface = $redirectinterface;
        $this->requestinterface = $requestinterface;
        $this->cart = $cart;
    }

    public function afterCheckQuoteItemQty(
        \Magento\CatalogInventory\Model\StockStateProvider $subject,
        $result,
        StockItemInterface $stockItem,
        $qty,
        $summaryQty,
        $origQty = 0
    ) {
        if (!$subject->checkQty($stockItem, $summaryQty) || !$subject->checkQty($stockItem, $qty)) {
            $referalUrl = $this->redirectinterface->getRedirectUrl();
            $urlArray = explode('/', $referalUrl);
            if (!in_array('checkout', $urlArray)) {
                $message = __('The requested qty is not available. Available Qty is '.$stockItem->getQty());
                $result->setHasError(true)
                       ->setMessage($message)
                       ->setQuoteMessage($message)
                       ->setQuoteMessageIndex('qty');
            } else {
                $itemsArray = $this->cart->getQuote()->getAllItems();
                $finaldata = [];
                $finaldata = $this->processItems($itemsArray);
                $messageVar = '';
                $i = 1;
                if ($finaldata) {
                    foreach ($finaldata as $finalMessage) {
                        $messageVar .= "<p class='avail-qty'>" . $i . ". Available Qty for " . implode(" is ", $finalMessage) . "</p> ";
                        $i++;
                    }
                    if (count($itemsArray) > count($finaldata)) {
                        $messageVar .= "<p class='inventory-note'><strong>Note : </strong>";
                        $messageVar .= "One of the products doesn't ";
                        $messageVar .= "have enough qty so currently ";
                        $messageVar .= "the cart is not updated.</p>";
                    }
                    $finalMessage1 = "<p class='request-qty'>The requested qty is not available.</p>" . $messageVar;
                    $message = __($finalMessage1);
                    $result->setHasError(true)
                           ->setMessage($message)
                           ->setQuoteMessage($message)
                           ->setQuoteMessageIndex('qty');
                }
            }
        }
        return $result;
    }

    public function processItems($itemsArray)
    {
        $cart_data = $this->requestinterface->getParam('cart');
        if ($cart_data) {
            foreach ($itemsArray as $key => $item) {
                if (array_key_exists($item->getItemId(), $cart_data)) {
                    $quoteItemArray = $cart_data[$item->getItemId()];
                    $productName = $item->getProduct()->getName();
                    $productId = $item->getProduct()->getId();
                    $stockInfo = $item->getProduct()->getExtensionAttributes()->getStockItem();
                    $productQty = $stockInfo->getQty();
                    foreach ($quoteItemArray as $quoteItemQty) {
                        if ($productQty < $quoteItemQty) {
                            $finaldata[] = [
                                '"'.$productName.'"',
                                "<span style='color:#A5001F;'>".$productQty."</span>"
                            ];
                        }
                    }
                }
            }
            return $finaldata;
        }
        return false;
    }
}

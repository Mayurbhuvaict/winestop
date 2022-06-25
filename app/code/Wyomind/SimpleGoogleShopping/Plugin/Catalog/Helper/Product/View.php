<?php

namespace Wyomind\SimpleGoogleShopping\Plugin\Catalog\Helper\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
class View
{
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * @param $subject
     * @param $resultPage
     * @param $product
     * @param null $params
     */
    public function beforeInitProductLayout($subject, $resultPage, $product, $params = null)
    {
        try {
            $currency = $this->request->getParam('currency');
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->storeManager->getStore();
            $availableCurrencies = $store->getAvailableCurrencyCodes();
            if (in_array($currency, $availableCurrencies)) {
                $store->setCurrentCurrencyCode($currency);
            }
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
        }
    }
}
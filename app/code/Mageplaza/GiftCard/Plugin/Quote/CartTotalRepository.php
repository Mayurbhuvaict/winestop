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

namespace Mageplaza\GiftCard\Plugin\Quote;

use Closure;
use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Model\Quote;
use Mageplaza\GiftCard\Helper\Checkout;
use Mageplaza\GiftCard\Model\GiftCard\Status;
use Mageplaza\GiftCard\Model\GiftCardFactory;

/**
 * Class CartTotalRepository
 * @package Mageplaza\GiftCard\Plugin\Quote
 */
class CartTotalRepository
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var TotalsExtensionFactory
     */
    protected $totalExtensionFactory;

    /**
     * @var Checkout
     */
    protected $checkoutHelper;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * CartTotalRepository constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param TotalsExtensionFactory $totalExtensionFactory
     * @param Checkout $gcCheckoutHelper
     * @param GiftCardFactory $giftCardFactory
     * @param RequestInterface $request
     * @param Repository $assetRepo
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        TotalsExtensionFactory $totalExtensionFactory,
        Checkout $gcCheckoutHelper,
        GiftCardFactory $giftCardFactory,
        RequestInterface $request,
        Repository $assetRepo,
        UrlInterface $urlBuilder
    ) {
        $this->quoteRepository       = $quoteRepository;
        $this->totalExtensionFactory = $totalExtensionFactory;
        $this->checkoutHelper        = $gcCheckoutHelper;
        $this->giftCardFactory       = $giftCardFactory;
        $this->request               = $request;
        $this->_assetRepo            = $assetRepo;
        $this->_urlBuilder           = $urlBuilder;
    }

    /**
     * @param CartTotalRepositoryInterface $subject
     * @param Closure $proceed
     * @param $cartId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundGet(CartTotalRepositoryInterface $subject, Closure $proceed, $cartId)
    {
        $quoteTotals = $proceed($cartId);

        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!($enableGiftCard = $this->checkoutHelper->canUsedGiftCard($quote))) {
            return $quoteTotals;
        }

        $customerBalance = $this->checkoutHelper->getCustomerBalance($quote->getCustomerId());
        $maxUsed         = min($customerBalance, $this->checkoutHelper->getTotalAmountForDiscount($quote, true));
        $giftCardConfig  = [
            'enableGiftCard'   => !$this->checkoutHelper->isUsedCouponBox() && $enableGiftCard,
            'enableMultiple'   => $this->checkoutHelper->isUsedMultipleCode(),
            'canShowDetail'    => (boolean) $this->checkoutHelper->getCheckoutConfig('show_detail'),
            'listGiftCard'     => $this->getGiftCardList(),
            'giftCardUsed'     => $this->getGiftCardsUsed($quote),
            'enableGiftCredit' => $this->checkoutHelper->canUsedCredit() && $enableGiftCard && ($maxUsed > 0.0001),
            'balance'          => $customerBalance,
            'maxUsed'          => $maxUsed,
            'creditUsed'       => $this->checkoutHelper->getGiftCreditUsed($quote),
            'css'              => [
                $this->getViewFileUrl('Mageplaza_Core/css/ion.rangeSlider.css'),
                $this->getViewFileUrl('Mageplaza_Core/css/skin/ion.rangeSlider.skinModern.css')
            ]
        ];

        $totalsExtension = $quoteTotals->getExtensionAttributes() ?: $this->totalExtensionFactory->create();
        $totalsExtension->setGiftCards(Checkout::jsonEncode($giftCardConfig));

        $quoteTotals->setExtensionAttributes($totalsExtension);

        return $quoteTotals;
    }

    /**
     * @param $quote
     *
     * @return array
     */
    public function getGiftCardsUsed($quote)
    {
        $giftCardUsed = $this->checkoutHelper->getGiftCardsUsed($quote);

        $result = [];
        foreach ($giftCardUsed as $code => $amount) {
            $giftCard = $this->giftCardFactory->create()
                ->loadByCode($code);
            if ($id = $giftCard->getId()) {
                $code = $this->checkoutHelper->isUsedCouponBox() ? $giftCard->getCode() : $giftCard->getHiddenCode();

                $result[$id] = [
                    'code'   => $code,
                    'amount' => $amount
                ];
            }
        }

        return $result;
    }

    /**
     * Get Gift Card List
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getGiftCardList()
    {
        $listResult = [];

        $customer = $this->checkoutHelper->getCustomer();
        if ($customer && $customer->getId()) {
            $giftCardList = $this->giftCardFactory->create()
                ->getGiftCardListForCustomer($this->checkoutHelper->getCustomer()->getId());
            foreach ($giftCardList as $giftCard) {
                if ((int) $giftCard['status'] !== Status::STATUS_ACTIVE) {
                    continue;
                }

                $listResult[$giftCard['giftcard_id']] = [
                    'code'        => $giftCard['code'],
                    'hidden_code' => $giftCard['hidden_code'],
                    'balance'     => $this->checkoutHelper->convertPrice($giftCard['balance'], true, false)
                ];
            }
        }

        return $listResult;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     *
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);

            return $this->_assetRepo->getUrlWithParams($fileId, $params);
        } catch (Exception $e) {
            return $this->_urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }
}

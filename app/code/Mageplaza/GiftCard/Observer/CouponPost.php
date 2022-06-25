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

namespace Mageplaza\GiftCard\Observer;

use Exception;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Escaper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\GiftCard\Helper\Checkout as DataHelper;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Psr\Log\LoggerInterface;

/**
 * Class CouponPost
 * @package Mageplaza\GiftCard\Observer
 */
class CouponPost implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var GiftCardFactory
     */
    protected $_giftcardFactory;

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CouponPost constructor.
     *
     * @param UrlInterface $url
     * @param Escaper $escaper
     * @param ManagerInterface $managerInterface
     * @param GiftCardFactory $giftcardFactory
     * @param DataHelper $dataHelper
     * @param CartRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlInterface $url,
        Escaper $escaper,
        ManagerInterface $managerInterface,
        GiftCardFactory $giftcardFactory,
        DataHelper $dataHelper,
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->_url             = $url;
        $this->escaper          = $escaper;
        $this->messageManager   = $managerInterface;
        $this->_giftcardFactory = $giftcardFactory;
        $this->_dataHelper      = $dataHelper;
        $this->quoteRepository  = $quoteRepository;
        $this->logger           = $logger;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     *
     * @return $this|Redirect
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->_dataHelper->isEnabled() || !$this->_dataHelper->isUsedCouponBox()) {
            return $this;
        }

        /** @var \Magento\Checkout\Controller\Cart\CouponPost $action */
        $action = $observer->getEvent()->getControllerAction();

        /** @var RequestInterface $request */
        $request    = $observer->getEvent()->getRequest();
        $couponCode = (int) $request->getParam('remove') === 1 ? '' : trim($request->getParam('coupon_code'));

        if ($couponCode === '') {
            /** @var Quote $quote */
            $quote = $this->_dataHelper->getCheckoutSession()->getQuote();
            if ($quote->getCouponCode()) {
                return $this;
            }

            $giftCards = $this->_dataHelper->getGiftCardsUsed($quote);
            if ($giftCards && count($giftCards)) {
                $this->_dataHelper->removeGiftCard(null, true, $quote);
                $this->messageManager->addSuccessMessage(__('You canceled the gift card code.'));

                return $this->_goBack($action);
            }

            return $this;
        }

        if ($this->_dataHelper->canUsedGiftCard()) {
            try {
                $this->_dataHelper->addGiftCards($couponCode);
                $this->messageManager->addSuccessMessage(__(
                    'You used gift card code "%1".',
                    $this->escaper->escapeHtml($couponCode)
                ));

                return $this->_goBack($action);
            } catch (Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\CouponPost $action
     *
     * @return $this
     */
    protected function _goBack($action)
    {
        $action->getActionFlag()->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $action->getResponse()->setRedirect($this->_url->getUrl('checkout/cart'));

        return $this;
    }
}

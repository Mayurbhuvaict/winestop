<?php
namespace Winestop\Custom\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;

class Remove extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CustomerCart $cart
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        CustomerCart $cart,
        \Magento\Framework\App\Response\RedirectInterface $redirect
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->cart = $cart;
        $this->redirect = $redirect;
        parent::__construct($context);
    }

    public function execute()
    {
        $product_ids = $this->getRequest()->getParam('product_ids');
        if ($product_ids) {
        	$product_ids_array = explode(",", $product_ids);  
        	$quote = $this->checkoutSession->getQuote();  
        	$quote_id  = $quote->getData('entity_id');
	        $quote = $this->quoteRepository->get($quote_id);
            $items = $quote->getAllVisibleItems();
            foreach ($items as $item) {
                $productId = $item->getProductId();
                if (in_array($productId, $product_ids_array)) {                   
                    $item->delete();                                    
                }                   
            }
            $quote->save();
            $this->cart->getQuote()->setTotalsCollectedFlag(false);
            $this->cart->save();
	        $response = [
                'success' => true,
                'redirectUrl' => $this->redirect->getRefererUrl()
            ];
	        $this->getResponse()->setContent(json_encode($response));
	    }
    }
}
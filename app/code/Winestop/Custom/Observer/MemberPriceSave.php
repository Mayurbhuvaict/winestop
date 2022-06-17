<?php

namespace Winestop\Custom\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Http\Context as AuthContext;
use \Magento\Framework\Event\ObserverInterface;

class MemberPriceSave implements ObserverInterface
{
	private $customerSession;
    private $authContext;
    private $productFactory;
    public function __construct(
    	Context $context,
    	Session $session,
    	AuthContext $authContext,
    	ProductFactory $productFactory
    )
    {
        $this->customerSession = $session;
        $this->authContext = $authContext;
        $this->productFactory = $productFactory;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if ($this->customerSession->isLoggedIn()) 
        {
			$cart = $observer->getEvent()->getData('cart');
			
			if($cart){
				if($quote = $cart->getQuote())
				{
					$itemVisible = $quote->getAllVisibleItems();
					$itemupdate = false;
					foreach ($itemVisible as $item) 
					{
						if($item->getParentItemId() || $item->getCustomPrice())
						{
							continue;
						}
						$productId = $this->productFactory->create()->getIdBySku($item->getSku());
						$product = $this->productFactory->create()->load($productId);
						if($product && $product->getId())
						{
							$price = $product->getMemberPrice();
							
							if($price)
							{
								$itemupdate = true;
								$item->setCustomPrice($price);
						        $item->setOriginalCustomPrice($price);
						        $item->getProduct()->setIsSuperMode(true);
						        $item->save();
							}
						}
					}
					if($itemupdate){

					}
				}
			}
        }
	}
}
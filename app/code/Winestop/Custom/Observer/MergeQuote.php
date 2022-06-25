<?php

namespace Winestop\Custom\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Http\Context as AuthContext;
use \Magento\Framework\Event\ObserverInterface;

class MergeQuote implements ObserverInterface
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
		
			$quote = $observer->getEvent()->getData('source');
			
			if($quote)
			{
				$itemVisible = $quote->getAllVisibleItems();
				$itemUpdate = false;
				foreach ($itemVisible as $item) 
				{
					if($item->getParentItemId())
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
							$itemUpdate = true;
							$item->setCustomPrice($price);
					        $item->setOriginalCustomPrice($price);
					        $item->getProduct()->setIsSuperMode(true);
					        $item->save();
						}
					}
				}
				if($itemUpdate){
					$quote->collectTotals()->save();
				}
			}
        
	}
}
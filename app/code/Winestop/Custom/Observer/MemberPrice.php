<?php 
namespace Winestop\Custom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session as CustomerSession;
/**
 * 
 */
class MemberPrice implements ObserverInterface
{
	 /**
    * @var CustomerSession
    */
    protected $customerSession;
    
    protected $dataHelper;
    protected $_productloader;
    /**
    * Add constructor.
    * @param CustomerSession $customerSession 
    */
 
    public function __construct(
    	CustomerSession $customerSession,
    	\Magento\Framework\Pricing\Helper\Data $dataHelper,
    	\Magento\Catalog\Model\ProductFactory $_productloader
    ) {
        $this->customerSession = $customerSession;
        $this->_productloader = $_productloader;
        $this->dataHelper = $dataHelper;
    }
	
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
        $item = $observer->getEvent()->getData('quote_item');         
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        $id = $item->getData('product_id');
        $_item = $this->_productloader->create()->load($id);
        $helper = $this->dataHelper;       
        $isLoggedIn = $this->customerSession->isLoggedIn();       
        if ($isLoggedIn) { 			
        	if ($_item->getData('member_price')) {   
            $member_price = $_item->getData('member_price');           
            $price = $member_price;
		    $item->setCustomPrice($price);
		    $item->setOriginalCustomPrice($price);
		    $item->getProduct()->setIsSuperMode(true);    

		    } else {
		    	$price = $item->getFinalPrice();
			    $item->setCustomPrice($price);
			    $item->setOriginalCustomPrice($price);
			    $item->getProduct()->setIsSuperMode(true);
		    }
        } else {
        	$price = $item->getFinalPrice();
		    $item->setCustomPrice($price);
		    $item->setOriginalCustomPrice($price);
		    $item->getProduct()->setIsSuperMode(true);
        }                                   
    }
}
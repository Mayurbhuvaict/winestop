<?php
namespace Winestop\Custom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
/**
 * 
 */
class Vars implements ObserverInterface
{
	protected $_messageFactory;
	protected $orderFactory;

	public function __construct(
		\Magento\Sales\Model\OrderFactory $orderFactory,
	    \Magento\GiftMessage\Model\MessageFactory $messageFactory
	)
	{
	    $this->_messageFactory = $messageFactory;
	    $this->orderFactory = $orderFactory;
	}
	public function execute(Observer $observer)
	{
		$giftMessage = $this->_messageFactory->create();	    

		$order = $observer->getSource();

        if (!$order instanceof \Magento\Sales\Model\Order) {
            $order = $order->getOrder();
        }        
		$orderIncrementId = $order->getData('increment_id'); 
		$order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);		
		$i=0;
        $item = $order->getAllVisibleItems();
        $grandTotalWeight1 = 0;
        $grandTotalWeight2 = 0;
        $shippingMethod = $order->getShippingMethod();
        $notallowCarrier = ['storepickup_storepickup','localshipping_localshipping'];
        foreach ($item as $value) 
        { 
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface')->getById($value->getProductId());
            $bottlesize=$product->getAttributeText('bottle_size');
            $bottlearray=['720ml','750ml','1 Liter','700ml'];
            $bottlearr=['1.5 Liter'];
            $config =$objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('Extrafee/Extrafee/less_one_liter');
            $values = (array)json_decode($config, true);
            $configlesstwoliter =$objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('Extrafee/Extrafee/less_two_liter');
            $lesstwoliter = (array)json_decode($configlesstwoliter, true);
        	$qty = (int)$value['qty_invoiced'];
        	if (in_array($bottlesize, $bottlearray)) 
        	{
                $qtyArray = [];
                $totalWeight='0.0';
                $itemlist[$i]['weight']  = $value->getWeight();
                $itemlist[$i]['quantity'] = $value->getQtyOrdered();
                $totalWeight += $itemlist[$i]['weight'] * $itemlist[$i]['quantity'];
                foreach ($values as $a => $val) 
                {
                    $n = explode('-', $val['box']);
                    $c = count($n);
                    if($c > 1) {
                        $range = range($n[0], $n[1]);
                        foreach ($range as $r) {
                           $qtyArray[$r] = $val;
                        }
                    } else {
                        $qtyArray[$n[0]] = $val;
                    }
                }
                $weight = 0;
                $fee = 0;
                if(array_key_exists($qty, $qtyArray)) {
                    $row = $qtyArray[$qty];
                    $weight = $row['weight'];

                    $fee = $row['fee'];
                }

                 if(!in_array($shippingMethod, $notallowCarrier)) {
                    $grandTotalWeight1 = $grandTotalWeight1 + $totalWeight + $weight;
                   }
                   else {

                       $grandTotalWeight1 = $grandTotalWeight1 + $totalWeight;
                   }

            } 
            else if (in_array($bottlesize, $bottlearr)) 
            {
                $qtyArray = [];
                $totalWeight='0.0';
                $itemlist[$i]['weight']  = $value->getWeight();
                $itemlist[$i]['quantity'] = $value->getQtyOrdered();
                $totalWeight += $itemlist[$i]['weight'] * $itemlist[$i]['quantity'];

                foreach ($lesstwoliter as $a => $val) {
                    $n = explode('-', $val['box']);
                    $c = count($n);
                    if($c > 1) {
                        $range = range($n[0], $n[1]);
                        foreach ($range as $r) {
                           $qtyArray[$r] = $val;
                        }
                    } else {
                        $qtyArray[$n[0]] = $val;
                    }
                }
                $weight = 0;
                $fee = 0;
                if(array_key_exists($qty, $qtyArray)) {
                    $row = $qtyArray[$qty];
                    $weight = $row['weight'];
                    $fee = $row['fee'];
                }
                
               if(!in_array($shippingMethod, $notallowCarrier)) {
                        $grandTotalWeight2 = $grandTotalWeight2 + $totalWeight + $weight;
                    } else {
                        $grandTotalWeight2 = $grandTotalWeight2 + $totalWeight;
                    }
            } 
    	}
		$payment = $order->getPayment();
   		$method = $payment->getMethodInstance();
    	$methodTitle = $method->getTitle();
        if ($methodTitle == NULL) {
            $methodTitle = 'The order was placed using ' . $order->getData('order_currency_code');
        }

    	if ($methodTitle) {
    		$value = "PAYMENT: \n" . $methodTitle;
    	   		$observer->getVariableList()->setData('invo_payment_method', $value);
    	}
    	else {
            $observer->getVariableList()->setData('invo_payment_method', '');
        }
    	if ($order->getData('shipping_description')) {
    		$value = "SHIPPING: \n" . $order->getData('shipping_description');
    	   		$observer->getVariableList()->setData('invo_shipping_method', $value);
    	}
    	else {
            $observer->getVariableList()->setData('invo_shipping_method', '');
        }   
	    if ($order->getGiftMessageId()) {
	        $giftMessage->load($order->getGiftMessageId());
                
	        $message =  $giftMessage->getMessage();	
            $value = "Gift Message \n"."From Name: ".$giftMessage->getSender() ."\nTo Name: ".$giftMessage->getRecipient()."\nMessage: ". $message;
            $observer->getVariableList()->setData('gift_message', $value);
        } else {
            $observer->getVariableList()->setData('gift_message', '');
        }
        if ($order->getAwOrderNote()) {
            $value = "Order Note: \n" . $order->getAwOrderNote();
            $observer->getVariableList()->setData('order_note', $value);
        } else {
            $observer->getVariableList()->setData('order_note', '');
        }
        if ($grandTotalWeight1) {
        	$value = "700ml,720ml,750ml and 1 liter: " . $grandTotalWeight1;
        	$observer->getVariableList()->setData('700ml_720ml_750ml_and_1_liter', $value);
        } else {
        	$observer->getVariableList()->setData('700ml_720ml_750ml_and_1_liter', '');
        }
        if ($grandTotalWeight2) {
        	$value = "1.5 Liter: " . $grandTotalWeight2;
        	$observer->getVariableList()->setData('1.5_Liter', $value);
        } else {
        	$observer->getVariableList()->setData('1.5_Liter', '');
        }
	}
}
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
    protected $datahelper;
    const BOX_UNIT = 'box'; 

    public function __construct(\Magento\Sales\Model\OrderFactory $orderFactory, \Magento\GiftMessage\Model\MessageFactory $messageFactory, \Magecomp\Extrafee\Helper\Data $datahelper)
    {
        $this->_messageFactory = $messageFactory;
        $this->orderFactory = $orderFactory;
        $this->datahelper = $datahelper;
    }
    public function execute(Observer $observer)
    {
        $handling = $this
            ->datahelper
            ->getHandlingConfig();
        $lastBelowOne = isset($handling[1]) ? end($handling[1]) : [];
        $lastBelowTwo = isset($handling[2]) ? end($handling[2]) : [];
        $giftMessage = $this
            ->_messageFactory
            ->create();

        $order = $observer->getSource();
        if (!$order instanceof \Magento\Sales\Model\Order)
        {
            $order = $order->getOrder();
        }
        $orderIncrementId = $order->getData('increment_id');
        $order = $this
            ->orderFactory
            ->create()
            ->loadByIncrementId($orderIncrementId);
        $item = $order->getAllVisibleItems();
        $grandTotalWeight1 = 0;
        $grandTotalWeight2 = 0;
        $shippingMethod = $order->getShippingMethod();
        $totalqty15 = 0;
        $totalqty750 = 0;
        $totalWeight15 = '0.0';
        $totalWeight750 = '0.0';
        $bottlearray = ['720ml', '750ml', '1 Liter', '700ml'];
        $bottlearr = ['1.5 Liter'];
        foreach ($item as $value)
        {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface')
                ->getById($value->getProductId());
            $bottlesize = $product->getAttributeText('bottle_size');
            $config = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue('Extrafee/Extrafee/less_one_liter');
            $values = (array)json_decode($config, true);
            /*$configlesstwoliter = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue('Extrafee/Extrafee/less_two_liter');
            $lesstwoliter = (array)json_decode($configlesstwoliter, true);*/
            if (in_array($bottlesize, $bottlearray))
            {
                $_weight = $value->getWeight();
                $_qty = $value->getQtyOrdered();
                $totalqty750 += $_qty;
                $totalWeight750 += $_weight * $_qty;
            }
            if (in_array($bottlesize, $bottlearr))
            {
                $_weight = $value->getWeight();
                $_qty = $value->getQtyOrdered();
                $totalqty15 += $_qty;
                $totalWeight15 += $_weight * $_qty;
            
            }
        }

        if ($totalqty750)
        {
            $boxweight=0;
            if($totalqty750 > $lastBelowTwo[self::BOX_UNIT])
            {
                $rest = (int) ($totalqty750 % $lastBelowOne[self::BOX_UNIT]);
                $_rest = (int) ($totalqty750 / $lastBelowOne[self::BOX_UNIT]);
                $boxweight = $lastBelowOne['weight']* $_rest;
                if(isset($handling[1][$rest])) {
                    $boxweight+=$handling[1][$rest]['weight'];
                }
            }
            else
            {
                if (array_key_exists((int)$totalqty750, $handling[1]))
                {
                    $row = $handling[1][$totalqty750];
                    $boxweight = $row['weight'];
                    $fee = $row['fee'];
                }
            }
            if ($shippingMethod != 'storepickup_storepickup')
            {
                $grandTotalWeight1 = $totalWeight750 + $boxweight;
            }
            else
            {
                $grandTotalWeight1 = $grandTotalWeight1 + $totalWeight750;
            }
        }

        if($totalqty15)
        {
            $boxweight=0;
            if ($totalqty15 > $lastBelowTwo[self::BOX_UNIT])
            {
                $rest = (int) ($totalqty15 % $lastBelowTwo[self::BOX_UNIT]);
                $_rest = (int) ($totalqty15 / $lastBelowTwo[self::BOX_UNIT]);
                $boxweight = $lastBelowTwo['weight'] * $_rest;
                if(isset($handling[2][$rest])) {
                $boxweight+=$handling[2][$rest]['weight'];
                }
            }
            else
            {
                if (array_key_exists((int)$totalqty15, $handling[2]))
                {
                    $row = $handling[2][$totalqty15];
                    $boxweight = $row['weight'];
                    $fee = $row['fee'];
                }
            }
            if ($shippingMethod != 'storepickup_storepickup')
            {
                $grandTotalWeight2 = $grandTotalWeight2 + $totalWeight15 + $boxweight;
            }
            else
            {
                $grandTotalWeight2 = $grandTotalWeight2 + $totalWeight15;
            }
        }
        
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getTitle();
        if ($methodTitle == NULL)
        {
            $methodTitle = 'The order was placed using ' . $order->getData('order_currency_code');
        }

        if ($methodTitle)
        {
            $value = $methodTitle;
            $observer->getVariableList()
                ->setData('invo_payment_method', $value);
        }
        else
        {
            $observer->getVariableList()
                ->setData('invo_payment_method', '');
        }
        if ($order->getData('shipping_description'))
        {
            $value = $order->getData('shipping_description');
            $observer->getVariableList()
                ->setData('invo_shipping_method', $value);
        }
        else
        {
            $observer->getVariableList()
                ->setData('invo_shipping_method', '');
        }
        if ($order->getGiftMessageId())
        {
            $giftMessage->load($order->getGiftMessageId());
            $message = $giftMessage->getMessage();
            $value = "Gift Message \n" . "From Name: " . $giftMessage->getSender() . "\nTo Name: " . $giftMessage->getSender() . "\nMessage: " . $message;
            $observer->getVariableList()
                ->setData('gift_message', $value);
        }
        else
        {
            $observer->getVariableList()
                ->setData('gift_message', '');
        }
        if ($order->getAwOrderNote())
        {
            $value = "Order Note: \n" . $order->getAwOrderNote();
            $observer->getVariableList()
                ->setData('order_note', $value);
        }
        else
        {
            $observer->getVariableList()
                ->setData('order_note', '');
        }
        if ($grandTotalWeight1)
        {
            $value = "700ml,720ml,750ml and 1 liter: " . $grandTotalWeight1;
            $observer->getVariableList()
                ->setData('700ml_720ml_750ml_and_1_liter', $value);
        }
        else
        {
            $observer->getVariableList()
                ->setData('700ml_720ml_750ml_and_1_liter', 'NA');
        }
        if ($grandTotalWeight2)
        {
            $value = "1.5 Liter: " . $grandTotalWeight2;
            $observer->getVariableList()
                ->setData('1.5_Liter', $value);
        }
        else
        {
            $observer->getVariableList()
                ->setData('1.5_Liter', '');
        }
    }
}

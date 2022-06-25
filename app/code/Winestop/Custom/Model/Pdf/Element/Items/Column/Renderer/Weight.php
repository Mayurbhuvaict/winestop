<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */

namespace Winestop\Custom\Model\Pdf\Element\Items\Column\Renderer;

/**
 *  Column renderer
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */
class Weight extends \Magetrend\PdfTemplates\Model\Pdf\Element\Items\Column\DefaultRenderer
{
	
    public function getRowValue()
    {
    	     $item =  $this->getItem();
    	     $proid=$item->getProductId();
    	     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	     $product = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface')->getById($item->getProductId());
    	       $bottlesize=$product->getAttributeText('bottle_size');
    	       $bottlearray=['720ml','750ml','1 Liter','700ml'];
    	       $bottlearr=['1.5 Liter'];
				$config =$objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('Extrafee/Extrafee/less_one_liter');
				$values = (array)json_decode($config, true);
				$configlesstwoliter =$objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('Extrafee/Extrafee/less_two_liter');
				$lesstwoliter = (array)json_decode($configlesstwoliter, true);

				$qty = (int)$item->getQty();
			    $prod_weight  = ($product->getWeight() * $qty);
			    $weight = 0;
				$fee = 0;
				if (in_array($bottlesize, $bottlearray)) {
					$qtyArray = [];

					foreach ($values as $a => $val) {
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
					if(array_key_exists($qty, $qtyArray)) {
					    $row = $qtyArray[$qty];
					    $weight = $row['weight'];
					    $fee = $row['fee'];
					}
				} 
				else if (in_array($bottlesize, $bottlearr)) {
					$qtyArray = [];

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
					if(array_key_exists($qty, $qtyArray)) {
					    $row = $qtyArray[$qty];
					    $weight = $row['weight'];
					    $fee = $row['fee'];
					}
				} 
			  $prod_weight  = ($product->getWeight() * $qty + $weight);
             return number_format((float)$prod_weight, 2, '.', '');
    }

   
}

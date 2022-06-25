<?php

namespace Wyomind\SimpleGoogleShopping\Plugin\Framework\Pricing\Render;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
class Amount
{
    protected $product;
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * @return float
     */
    public function beforeGetDisplayValue($subject)
    {
        try {
            $this->product = $this->registry->registry('current_product');
            $get = filter_input_array(INPUT_GET);
            if (isset($this->product) && $this->product->getId() && isset($get["ps"]) && $get["ps"] != '') {
                // only execute in product page context and with a ps parameter
                $om = ObjectManager::getInstance();
                $collection = $om->create("\\Magento\\Catalog\\Model\\ResourceModel\\Product\\Collection");
                $collection->getSelect()->join(["cpsl" => $collection->getTable("catalog_product_super_link")], "e.entity_id=cpsl.product_id and cpsl.parent_id = " . $this->product->getId(), []);
                $preSelection = explode("&", base64_decode($get["ps"]));
                foreach ($preSelection as $attributeInfo) {
                    $info = explode("=", $attributeInfo);
                    $attribute = $om->create("\\Magento\\Eav\\Model\\Entity\\Attribute")->load($info[0]);
                    $collection->addAttributeToSelect([$attribute->getAttributeCode()], true);
                    $collection->addAttributeToFilter($attribute->getAttributeCode(), $info[1]);
                }
                if (count($collection) == 1) {
                    $item = $collection->getFirstItem();
                    $productRepository = $om->create("\\Magento\\Catalog\\Model\\ProductRepository");
                    $product = $productRepository->get($item->getSku());
                    $selectionPrice = $product->getPriceInfo()->getPrice("final_price")->getAmount()->getValue();
                    $subject->setDisplayValue($selectionPrice);
                } else {
                    // fallback to normal rendering
                }
            }
        } catch (\Throwable $e) {
            // fallback to normal rendering
        }
    }
}
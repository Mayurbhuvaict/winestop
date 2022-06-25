<?php

namespace Winestop\Custom\Plugin\Product\ProductList;

class Toolbar
{
    /**
     * @var Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @param $collection
     */
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $collection
    ) {
            $currentOrder = $subject->getCurrentOrder();
            if ($currentOrder) {
                if ($currentOrder == 'high_to_low') {
                    $subject->getCollection()->setOrder('price', $subject->getCurrentDirection());
                } elseif ($currentOrder == 'low_to_high') {
                    $subject->getCollection()->setOrder('price', $subject->getCurrentDirection());
                }
                if ($subject->getCurrentOrder() == "latest") {
                    if ($subject->getRequest()->getParam('product_list_dir') != 'asc') {
                        $subject->getRequest()->setParam('product_list_dir', 'desc');
                    }
                    $collection->getSelect()->order('created_at '.$subject->getCurrentDirection());
                }
                if ($subject->getCurrentOrder() == "bestseller")
                {
                    $is_show = NULL;
                    $is_show = $subject->getRequest()->getParam('show-out-of-stock');
                    
                    $connection = $this->resourceConnection->getConnection();
                    $orderItemTable = $connection->getTableName('sales_order_item');
                    $query = "Select product_id FROM " . $orderItemTable . " GROUP BY product_id ORDER BY SUM(qty_ordered) DESC";
                    $result = $connection->fetchCol($query);
                    $arrayOfProductIds = $result;
                    $arrayOfProductIds = ($subject->getCurrentDirection() == 'desc') ? $result : array_reverse($result);
                    $sortedProductId = implode(",", $arrayOfProductIds);
                    $collection->getSelect()->reset(\Magento\Framework\DB\Select::WHERE);
                    $collection->addAttributeToFilter('type_id', ['neq' => 'mpgiftcard']);
                    if ($is_show == '1') {
                        $collection->getSelect()->order(
                            new \Zend_Db_Expr("FIELD(`e`.`entity_id`, $sortedProductId) desc")
                        );
                    } else {
                        $collection->getSelect()->where('stock_status_index.stock_status = ?',1)->order(new \Zend_Db_Expr("FIELD(`e`.`entity_id`, $sortedProductId) desc"));
                    }
                }
            } else {
                $subject->getCollection()->getSelect()->reset('order');
                $subject->getCollection()->setOrder('price', $subject->getCurrentDirection());
            }
            $result = $proceed($collection);
            return $result;       
    }  
}

<?php 
namespace Winestop\Custom\Block;
/**
 * 
 */
class Homepage extends \Magento\Framework\View\Element\Template
{
	
	  protected $_productCollectionFactory;
    protected $stockStateInterface;
    protected $_storeManager;
    protected $_productLabel;
    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productsFactory;

      public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
         \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Winestop\Custom\ViewModel\ProductLabel $productLabel,        
        array $data = []
        )
      {    
       $this->imageHelper = $imageHelper;
        $this->_productsFactory = $productsFactory;
       $this->_storeManager  = $storeManager;
       $this->productRepository = $productRepository;
       $this->stockStateInterface = $stockStateInterface;
       $this->_productCollectionFactory = $productCollectionFactory;
       $this->_productLabel = $productLabel;    
       parent::__construct($context, $data);
     }
       /**
        * Get Product Collection based on is show in home page Attribute 
        */
       public function getProductCollection()
       {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')->setPageSize(10);
        return $collection;
      }
          /**
        * Get Product ImageUrl 
        */
          public function getItemImage($productId)
          {
            try {
              $_product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
              return 'product not found';
            }
            $image_url = $this->imageHelper->init($_product, 'product_base_image')->getUrl();
            return $image_url;
          }
        /**
        * Check Product Status 
        */
        public function getStockItemStatus($product)
        {
          $result= $this->stockStateInterface->verifyStock($product->getId(), $product->getStore()->getWebsiteId());

          if ($result=='1') {
            $status='In Stock';
          }else{
            $status='Out Of Stock';
          }
          return $status;
        }
       
        /** Latest Products */

        public function getLatestProduct()
        {
          $collection = $this->_productCollectionFactory->create();
          $collection->addAttributeToSelect('*')->setFlag('has_stock_status_filter', false)
            ->joinField('stock_item', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1');
          $collection->addAttributeToSort('entity_id','desc')->addFieldToFilter('visibility', 4)->addFieldToFilter('attribute_set_id', 4)->setPageSize(15);

          return $collection;
        }

         /** Most Popular Products */

        public function getMostPopularProducts()
        {

           $storeId = $this->_storeManager->getStore()->getId();
          $collection = $this->_productsFactory->create()->addAttributeToSelect(
            '*'
        )->addViewsCount()->setStoreId(
            $storeId
        )->addStoreFilter(
            $storeId
        )->setPageSize(10);          
          
        return $collection;
        }
        	 /** Featured Products */
          public function getFeatureProducts()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*')->addAttributeToFilter('status', '1')
                        ->addAttributeToFilter('featured_product', '1');

        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        return $collection;
    }
    public function getAttributeobj(){
        return $this->_productLabel;
    }
}
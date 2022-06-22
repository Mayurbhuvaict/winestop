<?php 
namespace Winestop\Custom\Controller\Index;

/**
 * 
 */
class Deliverycheckercontroller extends \Magento\Framework\App\Action\Action
{
	
	protected $_resultRawFactory;
    protected $_connect;
    protected $helperdata;
    protected $_registry;
    protected $regionFactory;
    protected $_productloader;
    protected $cart;
    protected $_storeManager;
    protected $categoryRepository;
    protected $imagehelper;
    protected $priceHelper;

    public function __construct(
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection $connect,
        \Winestop\Custom\Helper\Data $helperdata,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Helper\Image $imagehelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    )
    {
        $this->_resultRawFactory = $resultRawFactory;
        $this->_connect = $connect;
        $this->helperdata = $helperdata;
        $this->_registry = $registry;
        $this->regionFactory = $regionFactory;
        $this->cart  = $cart;
        $this->_productloader = $_productloader;
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->imagehelper = $imagehelper;
        $this->priceHelper = $priceHelper;
        return parent::__construct($context);
    }

	 public function execute()
    {
        $response = [];
        $catname = $this->getRequest()->getParam('catname');
    	$code = $this->getRequest()->getParam('code');
        if (is_numeric($code)) {
            $shipperRegion = $this->regionFactory->create()->load($code);
            if($shipperRegion->getId()){
                $code = $shipperRegion->getCode();  
            }
        }
        $region = $this->regionFactory->create();
        $region = $region->loadByCode($code, 'US');
        $regionId = $region->getData('region_id') ;
        $region_name = $region->getData('default_name');        
        $only_wine_state = $this->helperdata->getConfig('custom/general/only_wine_state');
        $wsb_state = $this->helperdata->getConfig('custom/general/wsb_state');
        $only_wine_array = explode(",", $only_wine_state);
        $wsb_array = explode(",", $wsb_state);
        $items = $this->cart->getQuote()->getAllItems();
        $response['abel_to_change'] = true;
        if (in_array($code, $only_wine_array)) {
            $response['message'] = "DELIVER WINE ONLY";
        }
        if (in_array($code, $wsb_array)) {
            $response['message'] = "DELIVER WINE,SPIRITS & BEER";
        }
        $cnt = 1;
        if (count($items)) {
            foreach($items as $product) {
                $id = $product->getProductId();
                $item = $this->_productloader->create()->load($id);
                $categorys = $item->getCategoryCollection();
                $imageUrl = $this->imagehelper->init($item, 'product_base_image')
                    ->constrainOnly(true)
                    ->keepAspectRatio(true)
                    ->keepTransparency(true)
                    ->keepFrame(false)
                    ->resize(150, 150)->getUrl();
                    $price = $this->priceHelper->currency($product->getData('price'), true, false);                    
                foreach ($categorys as $category) {
                    $categoryId = $category->getData('entity_id');
                    $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
                    if ($category->getName() == 'Spirits' || $category->getName() == 'Beer') {
                        if (!in_array($code, $wsb_array)) {
                            $response['abel_to_change'] = false;
                            $response['item_count'] = $cnt;
                            $item_temp = array('item_id' => $id ,'image_url' => $imageUrl , 'price' => $price , 'name' => $item->getData('name'));
                            $response['item'][] = $item_temp;
                            $cnt++;
                        }                   
                    }
                    //print_r(get_class_methods($category));
                   // print_r(json_decode(json_encode($category->getData())));
                }
                    //exit();
            }
        }
        // echo $item->getProductId();
        // print_r(json_decode(json_encode($items)));
        if ($catname == 'Spirits' || $catname == 'Beer') {
            $connection = $this->_connect->getConnection();
    		$tableName = $this->_connect->getTableName('delivery_checker');            
            if (!in_array($code, $wsb_array)) {
                 $resultRaw = $this->_resultRawFactory->create();
                $response['status'] = true;
                //$response['message'] = 'Delivery available.';
            }
            else
            {
                $response['status'] = false;
                //$response['message'] = 'Delivery available.';
            }
        }
        $response['regionId'] = $regionId;
        $response['region_code'] = $code;
        $response['region_name'] = $region_name;
        // echo "<pre>";
        // print_r($response);
        // exit();
        $this->getResponse()->setContent(json_encode($response));
    }
}
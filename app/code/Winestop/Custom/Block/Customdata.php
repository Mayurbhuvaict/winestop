<?php 
namespace Winestop\Custom\Block;
use Magento\Framework\App\Http\Context as AuthContext;
/**
 * 
 */
class Customdata extends \Magento\Framework\View\Element\Template
{
	
	protected $orderRepository;
	protected $_customer;
	protected $_storemanager;
	protected $customerSession;
	protected $authContext;
	protected $_registry;
	protected $connection;
	protected $_productloader;
	protected $storeManager;

	public function __construct(
	    \Magento\Framework\View\Element\Template\Context $context,
	    \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
	    \Magento\Customer\Model\CustomerFactory $customer,
    	\Magento\Store\Model\StoreManagerInterface $storemanager,
    	\Magento\Customer\Model\Session $customerSession,
    	\Magento\Framework\Registry $registry,
    	\Magento\Framework\App\ResourceConnection $connection,
    	\Magento\Catalog\Model\ProductFactory $_productloader,
    	AuthContext $authContext,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
	    array $data = []
	){
	    $this->orderRepository = $orderRepository;
	    $this->_customer = $customer;
    	$this->_storemanager = $storemanager;
    	$this->customerSession = $customerSession;
    	$this->authContext = $authContext;
    	$this->_registry = $registry;
    	$this->connection = $connection;
    	$this->_productloader = $_productloader;
    	$this->storeManager = $storeManager;
	    parent::__construct($context, $data);
	}

	public function getOrder($id)
	{
	    return $this->orderRepository->get($id);
	}

	public function getCustomerbyname($email)
	{
		$websiteID = $this->_storemanager->getStore()->getWebsiteId();
    	$customer = $this->_customer->create()->setWebsiteId($websiteID)->loadByEmail($email);
    	return $customer;
	}

	public function isCustomerLoggedIn() {
		$isLoggedIn = $this->authContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);        
        return $isLoggedIn;
    }

    public function getWeAlsoRecomended()
    {
    	$product = $this->_registry->registry('current_product');
    	// categories this product is in
		$categories = $product->getCategoryIds();

		$storeId = $this->storeManager->getStore()->getId();

		// beer is special case because it has sub-sub categories
		if (in_array('46', $categories))
			$is_beer = true;
		else
			$is_beer = false;

		if (in_array('84', $categories))
			$is_domestic_beer = true;
		else
			$is_domestic_beer = false;
		
		if (in_array('85', $categories))
			$is_imported_beer = true;
		else
			$is_imported_beer = false;

		// don't include Shop Wines, Shop Spirits, Beer, Domestic or Imported Beer, or Featured Wines main categories
		$unwanted = array('12','13','14','46','84','85');

		$categories = array_diff($categories, $unwanted);

		// it's a beer, and isn't in a sub-sub category, then
		if ($is_beer && count($categories) == 0) {
			if ($is_domestic_beer)
				$categories[] = '84';
			else if ($is_imported_beer)
				$categories[] = '85';
			else
				$categories[] = '46';
		}

		$categories = implode(',',$categories);
		$conn = $this->connection->getConnection();
	    $select = $conn->select()
	          ->from(
	              ['cp' => 'catalog_category_product']
	          )
	          ->join(
	            ['pei' => 'catalog_product_entity_int'],
	            'pei.entity_id=cp.product_id'
	          )
	          ->joinNatural(
	            ['ea' => 'eav_attribute']
	          )
	          ->where('cp.category_id in (?)', $categories)
	          ->where('cp.product_id<>?', $product->getId())
	          ->where('pei.value=1')
	          ->where('ea.attribute_code="category_featured"')
	          ->where('pei.store_id', $storeId);
	    $rows = $conn->fetchAll($select);

	    // randomize list
		shuffle($rows);

		$this->_productCollection = array();

		// only return max number of products

		$productcount = 0;

		foreach ($rows as $row) {
			$productcount++;

			if ($productcount > 10)
				break;

			$this->_productCollection[] = $this->_productloader->create()->load($row['product_id']);
		}
		return $this->_productCollection;  
    }
}
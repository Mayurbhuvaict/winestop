<?php 
namespace Winestop\Custom\Controller\Index;

/**
 * 
 */
class Getshippingregion extends \Magento\Framework\App\Action\Action
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
	protected $_checkoutSession;

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
		\Magento\Checkout\Model\Session $_checkoutSession,
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
		$this->_checkoutSession = $_checkoutSession;
        $this->priceHelper = $priceHelper;
        return parent::__construct($context);
    }

	 public function execute()
    {
        $response = [];       
	   	$regionId = $this->_checkoutSession->getQuote()->getShippingAddress()->getRegionId(); //$this->cart->getQuote()->getShippingAddress()->getRegionId();		
        $response['regionId'] = $regionId;        
        $this->getResponse()->setContent(json_encode($response));
    }
}
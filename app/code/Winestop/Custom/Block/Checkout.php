<?php
namespace Winestop\Custom\Block;

class Checkout extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    protected $_storeManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
    }

    /**
     * Returns the product details for the purchase gtm event
     * @return array
     */

    public function getOrder() {
        return $this->_checkoutSession->getLastRealOrder();
    }

    public function getProducts() {
        $order = $this->_checkoutSession->getLastRealOrder();
        $products = [];
        
        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();

            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $children = $item->getChildren();
                foreach ($children as $child) {
                    $product = $child->getProduct();
                }
            }

            $productDetail = [];
            $productDetail['name'] = html_entity_decode($item->getName());
            $productDetail['id'] = $product->getSku();
            $productDetail['price'] = number_format($item->getPriceInclTax(), 2, '.', '');
            
            $productDetail['list'] = $categoryName;
            $productDetail['quantity'] = $item->getQty();
            $products[] = $productDetail;
        }

        return $products;
    }

    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }


}
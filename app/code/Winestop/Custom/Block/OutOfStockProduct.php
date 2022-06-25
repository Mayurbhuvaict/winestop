<?php

namespace Winestop\Custom\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Winestop\Custom\Registry\CurrentProduct;

/**
 * Webkul OutOfStockNotification ProductId Block
 */
class OutOfStockProduct extends \Magento\Framework\View\Element\Template
{
    /**
     * Magento Registry
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Magento Customer Session
     * @var Session
     */
    private $customerSession;

    /**
     * \Webkul\OutOfStockNotification\Helper\Data
     */
    private $helper;

    /**
     * StockRegistryInterface
     */
    private $stockRegistryInterface;

    protected $currentProduct;

    private $product;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param Session                                 $customerSession
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        StockRegistryInterface $stockRegistryInterface,
        SessionFactory $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Webkul\OutOfStockNotification\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        CurrentProduct $currentProduct,
        \Magento\Framework\ObjectManagerInterface $helperFactory,
        array $data = []
    ) {
        $this->currentProduct = $currentProduct;
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        $this->customer = $customer;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->helper = $helper;
        $this->productFactory = $productFactory;
        $this->helperFactory = $helperFactory;
        parent::__construct($context, $data);
    }

    /**
     * get Product Id of the product
     * @return integer
     */
    public function getProductId()
    {
        $productId = $this->registry->registry('current_product');
        $outOfStockProduct['id'] = $productId->getId();
        $outOfStockProduct['type'] = $productId->getTypeId();
        $productStockObj = $this->stockRegistryInterface->getStockItem($outOfStockProduct['id']);
        $outOfStockProduct['stock'] = $productStockObj->getData()['is_in_stock'];
        if ($outOfStockProduct['type'] == "configurable") {
            $result = $this->helper->getConfigStockStatus($outOfStockProduct['id']);
            $outOfStockProduct['oosn_associates'] = $result['oosn_associates'];
            $outOfStockProduct['stock'] = $result['stock_status'];
        }
        return $outOfStockProduct;
    }

    /**
     * getProduct function get the current product modal
     *
     * @return void
     */
    public function getProduct()
    {
        $product = $this->registry->registry('current_product');
        return $product;
    }

    /**
     * get Customer Email
     * @return string
     */
    public function getCustomerEmail()
    {
        $customerId = $this->customerSession->create()->getCustomer()->getId();
        $customerEmail = $this->customer->load($customerId)->getEmail();
        return $customerEmail;
    }
    public function helper($className)
    {
        $helper = $this->helperFactory->get($className);
        if (false === $helper instanceof \Magento\Framework\App\Helper\AbstractHelper) {
            throw new \LogicException($className . ' doesn\'t extends Magento\Framework\App\Helper\AbstractHelper');
        }
        return $helper;
    }
    public function getStockItem($productId)
    {
        try{
        return $this->stockRegistryInterface->getStockItem($productId)->getQty();
        }catch(\Exception $e){
            return 0;
        }
    }
    public function getCurrentProductQty()
    {
        $productId = $this->currentProduct->get()->getId();
        return $this->getStockItem($productId);
    }
}

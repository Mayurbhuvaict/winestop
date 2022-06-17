<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_OutOfStockNotification
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\OutOfStockNotification\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;

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
        \Magento\Framework\ObjectManagerInterface $helperFactory,
        array $data = []
    ) {
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
}

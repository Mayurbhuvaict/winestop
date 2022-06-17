<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */






























































































































































































































namespace Aheadworks\AdvancedReports\Observer;

use Aheadworks\AdvancedReports\Model\Log\ProductView as ProductViewLog;
use Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductView as ProductViewLogResource;
use Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductView\Collection as LogProductViewCollection;
use Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductView\CollectionFactory as LogProductViewCollectionFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Model\Visitor;
use Magento\Customer\Model\VisitorFactory;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CustomerLogin
 * @package Aheadworks\AdvancedReports\Observer
 */
class CustomerLogin implements ObserverInterface
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var ProductViewLogResource
     */
    private $productViewLogResource;

    /**
     * @var LogProductViewCollectionFactory
     */
    private $logProductViewCollectionFactory;

    /**
     * @var VisitorFactory
     */
    private $visitorFactory;

    /**
     * @param SessionManagerInterface $sessionManager
     * @param ProductViewLogResource $productViewLogResource
     * @param LogProductViewCollectionFactory $logProductViewCollectionFactory
     * @param VisitorFactory $visitorFactory
     */
    public function __construct(
        SessionManagerInterface $sessionManager,
        ProductViewLogResource $productViewLogResource,
        LogProductViewCollectionFactory $logProductViewCollectionFactory,
        VisitorFactory $visitorFactory
    ) {
        $this->sessionManager = $sessionManager;
        $this->productViewLogResource = $productViewLogResource;
        $this->logProductViewCollectionFactory = $logProductViewCollectionFactory;
        $this->visitorFactory = $visitorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(EventObserver $observer)
    {
        /** @var CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();
        $visitorData = $this->sessionManager->getVisitorData();

        if ($customer && $visitorData) {
            /** @var Visitor $visitor */
            $visitor = $this->visitorFactory->create();
            $visitor->setData($visitorData);

            if ($visitor->getId()) {
                /** @var LogProductViewCollection $logCollection */
                $logCollection = $this->logProductViewCollectionFactory->create();
                $logCollection
                    ->addFieldToFilter('visitor_id', ['eq' => $visitor->getId()])
                    ->addFieldToFilter('customer_id', ['null' => '']);
                /** @var ProductViewLog $item */
                foreach ($logCollection as $item) {
                    $item
                        ->setCustomerId($customer->getId())
                        ->setCustomerGroupId($customer->getGroupId());
                    $this->productViewLogResource->save($item);
                }
            }
        }
    }
}

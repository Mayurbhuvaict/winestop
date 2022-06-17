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





namespace Aheadworks\AdvancedReports\Controller\CountViews;

use Aheadworks\AdvancedReports\Model\Log\ProductView as ProductViewLog;
use Aheadworks\AdvancedReports\Model\Log\ProductViewFactory as ProductViewLogFactory;
use Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductView as ProductViewLogResource;
use Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductViewFactory as ProductViewLogResourceFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Visitor;
use Magento\Customer\Model\VisitorFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Product
 * @package Aheadworks\AdvancedReports\Controller\CountViews
 */
class Product extends \Magento\Framework\App\Action\Action
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var VisitorFactory
     */
    private $visitorFactory;

    /**
     * @var ProductViewLogFactory
     */
    private $productViewLogFactory;

    /**
     * @var ProductViewLogResourceFactory
     */
    private $productViewLogResourceFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Context $context
     * @param SessionManagerInterface $sessionManager
     * @param StoreManagerInterface $storeManager
     * @param VisitorFactory $visitorFactory
     * @param ProductViewLogResourceFactory $productViewLogResourceFactory
     * @param ProductViewLogFactory $productViewLogFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $sessionManager,
        StoreManagerInterface $storeManager,
        VisitorFactory $visitorFactory,
        ProductViewLogResourceFactory $productViewLogResourceFactory,
        ProductViewLogFactory $productViewLogFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->sessionManager = $sessionManager;
        $this->storeManager = $storeManager;
        $this->visitorFactory = $visitorFactory;
        $this->productViewLogResourceFactory = $productViewLogResourceFactory;
        $this->productViewLogFactory = $productViewLogFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $productId = $this->getRequest()->getParam('id');
            $storeId = $this->storeManager->getStore()->getId();
            $visitorData = $this->sessionManager->getVisitorData();
            if ($productId && $storeId && $visitorData) {
                /** @var Visitor $visitor */
                $visitor = $this->visitorFactory->create();
                $visitor->setData($visitorData);

                if ($visitor->getId()) {
                    $this->checkAndSaveProductView($productId, $visitor, $storeId);
                }
            }
        }
    }

    /**
     * Check and save new product view if needed
     *
     * @param int $productId
     * @param Visitor $visitor
     * @param int $storeId
     * @return void
     */
    private function checkAndSaveProductView($productId, $visitor, $storeId)
    {
        /** @var ProductViewLogResource $logResource */
        $logResource = $this->productViewLogResourceFactory->create();

        if (!$logResource->isExist($productId, $visitor->getId())) {
            /** @var ProductViewLog $productViewLog */
            $productViewLog = $this->productViewLogFactory->create();
            $productViewLog
                ->setProductId($productId)
                ->setVisitorId($visitor->getId())
                ->setStoreId($storeId);

            if ($visitor->getCustomerId()) {
                try {
                    /** @var CustomerInterface $customer */
                    $customer = $this->customerRepository->getById($visitor->getCustomerId());
                    $productViewLog->setCustomerId($customer->getId());
                    $productViewLog->setCustomerGroupId($customer->getGroupId());
                    // phpcs:disable Magento2.CodeAnalysis.EmptyBlock
                } catch (\Exception $e) {
                    //do nothing
                }
            }

            try {
                $logResource->save($productViewLog);
                // phpcs:disable Magento2.CodeAnalysis.EmptyBlock
            } catch (\Exception $e) {
                 //do nothing
            }
        }
    }
}

<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Controller\Adminhtml\Pool;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\PoolFactory;

/**
 * Class Grid
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Customer
 */
class Grid extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var PoolFactory
     */
    protected $_poolFactory;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param PoolFactory $poolFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PoolFactory $poolFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->resultLayoutFactory = $layoutFactory;
        $this->_coreRegistry       = $registry;
        $this->_poolFactory        = $poolFactory;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return Layout
     */
    public function execute()
    {
        $this->initCurrentPool();

        return $this->resultLayoutFactory->create();
    }

    /**
     * Pool initialization
     *
     * @return string pool id
     */
    protected function initCurrentPool()
    {
        $poolId = (int) $this->getRequest()->getParam('id');
        if (!$poolId && $this->getRequest()->getParam('pool_id')) {
            $poolId = (int) $this->getRequest()->getParam('pool_id');
        }

        /** @var Pool $pool */
        $pool = $this->_poolFactory->create();
        if ($poolId) {
            $pool->load($poolId);
            if (!$pool->getId()) {
                $this->messageManager->addErrorMessage(__('This gift card pool no longer exists.'));

                return false;
            }
        }

        $this->_coreRegistry->register('current_pool', $pool);

        return $pool;
    }
}

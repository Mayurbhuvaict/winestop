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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection;

/**
 * Class Delete
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class Delete extends Pool
{
    /**
     * @var Collection
     */
    protected $giftCardCollection;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param Collection $giftCardCollection
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        Collection $giftCardCollection
    ) {
        $this->giftCardCollection = $giftCardCollection;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $giftPool = $this->_initObject();

        if ($giftPool && $giftPool->getId()) {
            try {
                $collection = $this->giftCardCollection
                    ->addFieldToFilter('pool_id', $giftPool->getId());
                $collection->walk('delete');

                $giftPool->delete();
                $this->messageManager->addSuccessMessage(__('The gift pool was deleted successfully.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        $this->_redirect('*/*/');
    }
}

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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection;
use Zend_Db_Select;

/**
 * Class MassDelete
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Template
 */
class MassDelete extends Pool
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var Collection
     */
    protected $giftCardCollection;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param Filter $filter
     * @param Collection $giftCardCollection
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        Filter $filter,
        Collection $giftCardCollection
    ) {
        $this->filter             = $filter;
        $this->giftCardCollection = $giftCardCollection;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->_getPoolCollection());
        $deleted    = 0;

        foreach ($collection->getItems() as $pool) {
            $collection = $this->giftCardCollection
                ->addFieldToFilter('pool_id', $pool->getId());
            $collection->walk('delete');
            $this->giftCardCollection->clear()->getSelect()->reset(Zend_Db_Select::WHERE);

            $pool->delete();
            $deleted++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}

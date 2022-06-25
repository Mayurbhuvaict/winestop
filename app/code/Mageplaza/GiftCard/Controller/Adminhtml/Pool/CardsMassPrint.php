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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection;
use Spipu\Html2Pdf\Exception\Html2PdfException;

/**
 * Class CardsMassPrint
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class CardsMassPrint extends Pool
{
    /**
     * @var Template
     */
    protected $_template;

    /**
     * CardsMassPrint constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param Template $templateHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        Template $templateHelper
    ) {
        $this->_template = $templateHelper;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Html2PdfException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $pool = $this->_initObject();

        if (!$pool->getId()) {
            $this->_forward('noroute');
        }

        $codesIds = $this->getRequest()->getParam('ids');

        if (is_array($codesIds)) {
            /** @var Collection $collection */
            $collection = $this->_objectManager->create(Collection::class)
                ->addFieldToFilter('giftcard_id', ['in' => $codesIds])
                ->addFieldToFilter('pool_id', $pool->getId());

            $output = $this->_template->outputGiftCardPdf($collection->getItems(), 'D');

            if ($output === null) {
                $this->messageManager->addErrorMessage(__('Gift cards can\'t print.'));
                $this->_redirect('*/*/');
            }
        }
    }
}

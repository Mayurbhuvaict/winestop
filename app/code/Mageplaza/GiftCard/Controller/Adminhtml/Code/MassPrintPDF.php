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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection;
use Spipu\Html2Pdf\Exception\Html2PdfException;

/**
 * Class MassPrintPDF
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Code
 */
class MassPrintPDF extends Code
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /** @var  Template */
    protected $_template;

    /**
     * MassPrintPDF constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     * @param Filter $filter
     * @param Template $template
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory,
        Filter $filter,
        Template $template
    ) {
        $this->filter    = $filter;
        $this->_template = $template;

        parent::__construct($context, $resultPageFactory, $giftCardFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws LocalizedException
     * @throws Html2PdfException
     */
    public function execute()
    {
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->_getCodeCollection());
        $output     = $this->_template->outputGiftCardPdf($collection->getItems(), 'D');

        if ($output === null) {
            $this->messageManager->addErrorMessage(__('Gift cards can\'t print.'));
            $this->_redirect('*/*/');
        }
    }
}

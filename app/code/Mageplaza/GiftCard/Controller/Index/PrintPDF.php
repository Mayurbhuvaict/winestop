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

namespace Mageplaza\GiftCard\Controller\Index;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Spipu\Html2Pdf\Exception\Html2PdfException;

/**
 * Class PrintPDF
 * @package Mageplaza\GiftCard\Controller\Index
 */
class PrintPDF extends AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /** @var GiftCardFactory */
    protected $_giftCardFactory;

    /** @var  Template */
    protected $_template;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     * @param Template $template
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory,
        Template $template
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_giftCardFactory  = $giftCardFactory;
        $this->_template         = $template;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Html2PdfException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $output   = null;
        $giftCard = $this->_giftCardFactory->create()->load($this->_request->getParam('id'));
        if ($giftCard->getId()) {
            $output = $this->_template->outputGiftCardPdf($giftCard, 'D');
        }

        if ($output === null) {
            $this->messageManager->addErrorMessage(__('Gift cards can\'t print.'));
            $this->_redirect('*/*/');
        }
    }
}

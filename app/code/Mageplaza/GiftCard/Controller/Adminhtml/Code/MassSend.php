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

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Psr\Log\LoggerInterface;
use Twilio\Exceptions\TwilioException;

/**
 * Class MassSend
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Code
 */
class MassSend extends Code
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MassSend constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     * @param Filter $filter
     * @param LoggerInterface $log
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory,
        Filter $filter,
        LoggerInterface $log
    ) {
        $this->filter = $filter;
        $this->logger = $log;

        parent::__construct($context, $resultPageFactory, $giftCardFactory);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->_getCodeCollection());
        $codeSent   = 0;

        /** @var GiftCard $giftCard */
        foreach ($collection->getItems() as $giftCard) {
            if ($giftCard->getDeliveryMethod() && $giftCard->getDeliveryAddress()) {
                try {
                    $giftCard->setData('send_to_recipient', true)
                        ->save();
                    $codeSent++;
                } catch (TwilioException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }
        if ($codeSent) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been sent.', $codeSent)
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}

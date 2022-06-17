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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManager;
use Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Product\DeliveryMethods;
use Psr\Log\LoggerInterface;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Twilio\Exceptions\TwilioException;

/**
 * Class Save
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Code
 */
class Save extends Code
{
    /**
     * @var Template
     */
    private $templateHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GiftCardFactory $giftCardFactory
     * @param Template $templateHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GiftCardFactory $giftCardFactory,
        Template $templateHelper,
        LoggerInterface $logger
    ) {
        $this->templateHelper = $templateHelper;
        $this->logger         = $logger;

        parent::__construct($context, $resultPageFactory, $giftCardFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Html2PdfException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            // init model and set data
            $giftCard = $this->_initObject();

            $action = $this->getRequest()->getParam('action');
            if (($action === 'print') && $giftCard->getId()) {
                $this->templateHelper->outputGiftCardPdf($giftCard, 'D');

                return;
            }

            $this->prepareData($data);
            if (!empty($data)) {
                $giftCard->addData($data);
            }

            if ($action === 'send') {
                if ($giftCard->getDeliveryMethod() && $giftCard->getDeliveryAddress()) {
                    $giftCard->setData('send_to_recipient', true);
                } else {
                    $this->messageManager->addNoticeMessage(__('Gift card is not sent. Please correct the delivery information.'));
                }
            }

            // try to save it
            try {
                $giftCard->save();

                $this->messageManager->addSuccessMessage(__('The Gift Card Code has been saved successfully.'));

                // clear previously saved data from session
                $this->_getSession()->unsData('giftcard_code_form');

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back') === 'edit') {
                    $this->_redirect('*/*/edit', ['id' => $giftCard->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (TwilioException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving gift code.'));
            }
            $this->_getSession()->setData('giftcard_code_form', $data);

            // redirect to edit form
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function prepareData(&$data)
    {
        if (isset($data['template_fields'])) {
            $data['template_fields'] = Template::jsonEncode($data['template_fields']);
        }
        if (isset($data['delivery_method'])) {
            $fieldName                = DeliveryMethods::getFormFieldName($data['delivery_method']);
            $data['delivery_address'] = isset($data[$fieldName]) ? $data[$fieldName] : '';
        }
        $data['action_vars'] = ['auth' => $this->_auth->getUser()->getName()];

        $store = $this->_objectManager->get(StoreManager::class);
        if ($store->isSingleStoreMode()) {
            $data['store_id'] = $store->getStore(true)->getId();
        }
        if (isset($data['rule'])) {
            $data['conditions_serialized'] = $this->templateHelper->convertConditions($data['rule']);
        }

        return $this;
    }
}

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

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonDataHelper;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\CreditFactory;

/**
 * Class Settings
 * @package Mageplaza\GiftCard\Controller\Index
 */
class Settings extends Action
{
    /**
     * @var JsonDataHelper $helper
     */
    protected $helper;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var CreditFactory
     */
    protected $creditFactory;

    /**
     * @var DataHelper
     */
    protected $giftCardHelper;

    /**
     * Settings constructor.
     *
     * @param Context $context
     * @param JsonDataHelper $helper
     * @param JsonFactory $resultJsonFactory
     * @param RawFactory $resultRawFactory
     * @param CreditFactory $creditFactory
     * @param DataHelper $giftCardHelper
     */
    public function __construct(
        Context $context,
        JsonDataHelper $helper,
        JsonFactory $resultJsonFactory,
        RawFactory $resultRawFactory,
        CreditFactory $creditFactory,
        DataHelper $giftCardHelper
    ) {
        $this->helper            = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory  = $resultRawFactory;
        $this->giftCardHelper    = $giftCardHelper;
        $this->creditFactory     = $creditFactory;

        parent::__construct($context);
    }

    /**
     * Login registered users and initiate a session.
     *
     * Expects a POST. ex for JSON {"username":"user@magento.com", "password":"password"}
     *
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $credentials        = null;
        $httpBadRequestCode = 400;

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        if (!$this->giftCardHelper->isEnabled() || !$this->getRequest()->isAjax()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        try {
            $credentials = $this->helper->jsonDecode($this->getRequest()->getContent());
        } catch (Exception $e) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }
        if (!$credentials || $this->getRequest()->getMethod() !== 'POST' || !$this->getRequest()->isXmlHttpRequest()) {
            return $resultRaw->setHttpResponseCode($httpBadRequestCode);
        }

        $response = [
            'errors'  => false,
            'message' => __('Notification Save Successfully.')
        ];
        try {
            $customer = $this->giftCardHelper->getCustomer();
            if (!$customer || !$customer->getId()) {
                throw new LocalizedException(__('Customer does not exist.'));
            }

            $customerCredit = $this->creditFactory->create()->load($customer->getId(), 'customer_id');
            if (!$customerCredit->getId()) {
                $customerCredit->setCustomerId($customer->getId());
            }

            $customerCredit->addData($credentials)->save();
        } catch (LocalizedException $e) {
            $response = [
                'errors'  => true,
                'message' => $e->getMessage()
            ];
        } catch (Exception $e) {
            $response = [
                'errors'  => true,
                'message' => __('Invalid data.')
            ];
        }
        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($response);
    }
}

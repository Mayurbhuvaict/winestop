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




















namespace Aheadworks\AdvancedReports\Controller\Adminhtml\ProductPerformance;

use Aheadworks\AdvancedReports\Model\Url\Base64Coder;
use Aheadworks\AdvancedReports\Ui\Component\Listing\Breadcrumbs;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Aheadworks\AdvancedReports\Controller\Adminhtml\ProductPerformance
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReports::reports_productperformance';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $couponCode = $this->_request->getParam('coupon_code');
        $manufacturer = $this->_request->getParam('manufacturer');
        $paymentName = $this->_request->getParam('payment_name');
        $categoryName = $this->_request->getParam('category_name');
        if ($couponCode) {
            $title = __('Product Performance (%1)', Base64Coder::decode($couponCode));
        } elseif ($manufacturer) {
            $title = __('Product Performance (%1)', Base64Coder::decode($manufacturer));
        } elseif ($paymentName) {
            $title = __('Product Performance (%1)', Base64Coder::decode($paymentName));
        } elseif ($categoryName) {
            $title = __('Product Performance (%1)', Base64Coder::decode($categoryName));
        } else {
            $title = __('Product Performance');
        }
        $this->_session->setData(Breadcrumbs::BREADCRUMBS_CONTROLLER_TITLE, $title);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_AdvancedReports::reports_productperformance');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}

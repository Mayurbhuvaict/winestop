<?php
/**
 * Webkul Software
 *
 * @category    Webkul
 * @package     Webkul_OutOfStockNotification
 * @author      Webkul
 * @copyright   Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license     https://store.webkul.com/license.html
 */

namespace Webkul\OutOfStockNotification\Controller\Product;

use Magento\Framework\App\Action\Context;
use Webkul\OutOfStockNotification\Helper\Data;

/**
 * AddCustomer Class
 */
class ConfigProduct extends \Magento\Framework\App\Action\Action
{
    /**
     * @var  use Webkul\OutOfStockNotification\Helper\Data
     */
    private $helper;

    /**
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJson;
    
    /**
     * @param Context                                             $context
     * @param Data                                                $helper
     * @param \Magento\Framework\Controller\Result\JsonFactory    $resultJson
     */
    public function __construct(
        Context $context,
        Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJson
    ) {
        $this->resultJson = $resultJson;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $id=$this->getRequest()->getParam('productId');
            $helperData = $this->helper;
            $result = $helperData->getStockStatus($id);
            return $this->resultJson->create()->setData($result);
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
    }
}

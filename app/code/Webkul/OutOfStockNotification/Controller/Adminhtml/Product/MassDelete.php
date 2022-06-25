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
namespace Webkul\OutOfStockNotification\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Webkul\OutOfStockNotification\Helper\Data;

/*
 * MassNotified Class
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     *
     * @var \Webkul\OutOfStockNotification\Model\Product
     */
    private $productModel;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     *
     * @param Action\Context                                                    $context
     * @param \Webkul\OutOfStockNotification\Model\Product                      $productModel
     */
    public function __construct(
        Action\Context $context,
        \Webkul\OutOfStockNotification\Model\Product $productModel,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        $this->productModel = $productModel;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $notificationModel = $this->productModel;
        $model = $this->filter;
        $collection = $model->getCollection($notificationModel->getCollection());
        try {
            foreach ($collection as $product) {
                $this->deleteProduct($product);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->messageManager->addSuccess(__('Request(s) deleted successfully.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('outofstocknotification/product/preorderrequests');
    }

    /**
     * delete
     *
     * @param object $product
     * @return void
     */
    private function deleteProduct($product)
    {
        $product->delete();
    }
}

<?php
/**
 * FAQ Category Controller for backend-side FAQ Category.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Controller\Adminhtml\Faqcat;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Rootways\Faq\Model\ResourceModel\Faqcat;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    protected $_coreRegistry;
    protected $_directoryList;
    protected $_fileUploaderFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_directoryList = $directoryList;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
    }
	
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $data['name'] = $data['name'];
            $data['status'] = $data['status'];

            $id = $this->getRequest()->getParam('cat_id');
            $model = $this->_objectManager->create('Rootways\Faq\Model\Faqcat')->load($id);
            if (!$model->getCatId() && $id) {
                $this->messageManager->addError(__('This Category no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the Category.'));

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['cat_id' => $model->getCatId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Category: '.$e->getMessage()));
            }

            $this->_getSession()->setFormData($data);
            if ($this->getRequest()->getParam('cat_id')) {
                return $resultRedirect->setPath('*/*/edit', ['cat_id' => $this->getRequest()->getParam('cat_id')]);
            }
            return $resultRedirect->setPath('*/*/new');
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        if ($this->_authorization->isAllowed('Rootways_Faq::category_create') || $this->_authorization->isAllowed('Rootways_Faq::category_edit')) {
            return true;
        }
        return false;
    }
}

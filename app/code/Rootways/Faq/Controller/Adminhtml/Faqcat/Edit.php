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

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Rootways_Faq::category_edit';
	protected $_coreRegistry;
	protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('cat_id');
        $model = $this->_objectManager->create('Rootways\Faq\Model\Faqcat');
        $model->setData([]);
        if ($id && (int) $id > 0) {
            $model->load($id);
            if (!$model->getCatId()) {
                $this->messageManager->addError(__('This Category no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $name = $model->getName();
        }
		
        $FormData = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($FormData)) {
            $model->setData($FormData);
        }

        $this->_coreRegistry->register('rootways_faqcat', $model);
		$resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rootways_Faq::faqs');
		
        $resultPage->addBreadcrumb(
            $id ? __('Edit Category') : __('New Category'),
            $id ? __('Edit Category') : __('New Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('FAQ Categories Manager'));
        $resultPage->getConfig()->getTitle()
            ->prepend($id ? 'Edit: '.$name.' ('.$id.')' : __('New Category'));

        return $resultPage;
    }
}

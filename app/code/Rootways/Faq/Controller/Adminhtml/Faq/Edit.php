<?php
/**
 * FAQ Controller for backend-side FAQ.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Controller\Adminhtml\Faq;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Rootways_Faq::faq_edit';

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
        $id = $this->getRequest()->getParam('faq_id');
        $model = $this->_objectManager->create('Rootways\Faq\Model\Faq');
        $model->setData([]);
        if ($id && (int) $id > 0) {
            $model->load($id);
            if (!$model->getFaqId()) {
                $this->messageManager->addError(__('This FAQ no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $title = $model->getTitle();
        }

        $formData = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($formData)) {
            $model->setData($formData);
        }

        $this->_coreRegistry->register('rootways_faq', $model);

		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Rootways_Faq::faqs');
        $resultPage->addBreadcrumb(
            $id ? __('Edit FAQ') : __('New FAQ'),
            $id ? __('Edit FAQ') : __('New FAQ')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('FAQs Manager'));
        $resultPage->getConfig()->getTitle()
            ->prepend($id ? 'Edit: '.$title.' ('.$id.')' : __('New FAQ'));

        return $resultPage;
    }
}

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

use Rootways\Faq\Model\ResourceModel\Faq;

class Delete extends \Magento\Backend\App\Action
{
	const ADMIN_RESOURCE = 'Rootways_Faq::faq_delete';
    public function execute()
    {
        $faq_id = $this->getRequest()->getParam('faq_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($faq_id && (int) $faq_id > 0) {
            $title = '';
            try {
                $model = $this->_objectManager->create('Rootways\Faq\Model\Faq');
                $model->load($faq_id);
                if ($model->getFaqId()) {
                    $title = $model->getTitle();

                    $model->delete();

                    $this->messageManager->addSuccess(__('The "'.$title.'" FAQ has been deleted.'));
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['faq_id' => $faq_id]);
            }
        }
        $this->messageManager->addError(__('FAQ to delete was not found.'));
        return $resultRedirect->setPath('*/*/');
    }
}

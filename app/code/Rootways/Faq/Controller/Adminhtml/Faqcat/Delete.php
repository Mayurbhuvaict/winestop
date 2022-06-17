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

use Rootways\Faq\Model\ResourceModel\Faqcat;

class Delete extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $cat_id = $this->getRequest()->getParam('cat_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($cat_id && (int) $cat_id > 0) {
            $name = '';
            try {
                $model = $this->_objectManager->create('Rootways\Faq\Model\Faqcat');
                $category = $model->load($cat_id);

                if ($category->getCatId()) {
                    $name = $model->getName();
                    $model->delete();
                    $this->messageManager->addSuccess(__('The "'.$name.'" Category was successfully deleted.')); 
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['cat_id' => $cat_id]);
            }
        }
        $this->messageManager->addError(__('Category to delete was not found.'));
        return $resultRedirect->setPath('*/*/');
    }
}

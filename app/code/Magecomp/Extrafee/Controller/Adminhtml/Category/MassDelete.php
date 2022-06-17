<?php

namespace Magecomp\Extrafee\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magecomp\Extrafee\Controller\Adminhtml\Category;

class MassDelete extends Category
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $ids = $this->getRequest()->getParam('category');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select records'));
            $this->_redirect('*/*/');
            return;
        }

        $deleted = 0;
        foreach ($ids as $id) {
            $this->categoryFactory->create()->load($id)->delete();
            $deleted++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        $this->_redirect('*/*/');
        return;
    }
}

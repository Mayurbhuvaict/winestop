<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Controller\Adminhtml\Job;


use Magento\Framework\Controller\ResultFactory;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Controller\Adminhtml\Job;

class Details extends Job
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id    = $this->getRequest()->getParam(JobInterface::ID);
        $model = $this->initModel();

        if ($id && !$model) {
            $this->messageManager->addErrorMessage((string)__('This job no longer exists.'));

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        
        $this->addDisabledWarningMessage();

        $resultPage->getConfig()->getTitle()->prepend((string)__('Job #' . $id . ' - ' . date('M d, Y', strtotime($model->getStartedAt()))));
        $this->_initAction();

        return $resultPage;
    }
}

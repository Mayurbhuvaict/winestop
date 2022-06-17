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



namespace Mirasvit\SeoContent\Controller\Adminhtml\Rewrite;

use Mirasvit\SeoContent\Api\Data\RewriteInterface;
use Mirasvit\SeoContent\Controller\Adminhtml\Rewrite;

class Delete extends Rewrite
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(RewriteInterface::ID);

        if ($id) {
            try {
                $model = $this->rewriteRepository->get($id);
                $this->rewriteRepository->delete($model);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->messageManager->addSuccessMessage(
                __('Rewrite was removed')
            );
        } else {
            $this->messageManager->addErrorMessage(__('Please select rewrite'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}

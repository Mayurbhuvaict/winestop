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

class Save extends Rewrite
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(RewriteInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();

        if ($data) {
            $model          = $this->initModel();
            $resultRedirect = $this->resultRedirectFactory->create();

            if ($id && !$model) {
                $this->messageManager->addErrorMessage(__('This rewrite no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if (!isset($data[RewriteInterface::DESCRIPTION_POSITION])) {
                $data[RewriteInterface::DESCRIPTION_POSITION] = RewriteInterface::DESCRIPTION_POSITION_DISABLED;
            }

            if (!isset($data[RewriteInterface::META_ROBOTS])) {
                $data[RewriteInterface::META_ROBOTS] = '';
            }

            $data = $this->contentService->escapeJS($data);

            $model->setUrl($data[RewriteInterface::URL])
                ->setIsActive((bool)$data[RewriteInterface::IS_ACTIVE])
                ->setSortOrder($data[RewriteInterface::SORT_ORDER])
                ->setTitle($data[RewriteInterface::TITLE])
                ->setMetaTitle($data[RewriteInterface::META_TITLE])
                ->setMetaKeywords($data[RewriteInterface::META_KEYWORDS])
                ->setMetaDescription($data[RewriteInterface::META_DESCRIPTION])
                ->setDescription($data[RewriteInterface::DESCRIPTION])
                ->setDescriptionPosition($data[RewriteInterface::DESCRIPTION_POSITION])
                ->setDescriptionTemplate($data[RewriteInterface::DESCRIPTION_TEMPLATE])
                ->setMetaRobots($data[RewriteInterface::META_ROBOTS])
                ->setStoreIds($data[RewriteInterface::STORE_IDS]);

            try {
                $this->rewriteRepository->save($model);

                $this->messageManager->addSuccessMessage(__('Rewrite was saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [RewriteInterface::ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [RewriteInterface::ID => $id]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }
}

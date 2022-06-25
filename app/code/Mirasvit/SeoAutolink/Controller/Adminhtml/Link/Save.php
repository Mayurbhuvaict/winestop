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

namespace Mirasvit\SeoAutolink\Controller\Adminhtml\Link;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Mirasvit\SeoAutolink\Controller\Adminhtml\Link
{
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute(): void
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if ($data = $this->getRequest()->getParams()) {
            $data['keyword'] = trim((string)$data['keyword']);
            $data = $this->prepareStoreIds($data);
            $model = $this->_initModel();
            $model->setData($data);

            try {
                $model->save();

                $this->messageManager->addSuccess((string)__('Link was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addError((string)__('Unable to find link to save'));
        $this->_redirect('*/*/');
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareStoreIds($data)
    {
        if (isset($data['use_config']['store_ids'])
            && $data['use_config']['store_ids'] == 'true') {
            $data['store_ids'] = [0];
        } elseif (isset($data['store_id'])) {
            $data['store_ids'] = $data['store_id'];
        }

        return $data;
    }
}

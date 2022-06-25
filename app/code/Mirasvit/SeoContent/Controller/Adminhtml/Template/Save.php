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



namespace Mirasvit\SeoContent\Controller\Adminhtml\Template;

use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Controller\Adminhtml\Template;
use Mirasvit\Core\Service\SerializeService;

class Save extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(TemplateInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();

        if ($data) {
            $model          = $this->initModel();
            $resultRedirect = $this->resultRedirectFactory->create();

            if ($id && !$model) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $data = $this->filter($data, $model);
            $data = $this->contentService->escapeJS($data);

            $model = $this->fillModelWithData($model, $data);

            try {
                $this->templateRepository->save($model);

                $this->messageManager->addSuccessMessage(__('Template was saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [TemplateInterface::ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [TemplateInterface::ID => $id]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array             $data
     * @param TemplateInterface $template
     *
     * @return array
     */
    protected function filter(array $data, TemplateInterface $template)
    {
        $rule = $template->getRule();

        $conditions = $template->getRule()->getConditions()->asArray();

        if (isset($data['rule']) && isset($data['rule']['conditions'])) {
            $rule->loadPost(['conditions' => $data['rule']['conditions']]);

            $conditions = $rule->getConditions()->asArray();
        }

        if (CompatibilityService::is21()) {
            $conditions = SerializeService::encode($conditions);
        } else {
            $conditions = \Zend_Json::encode($conditions);
        }

        $data[TemplateInterface::CONDITIONS_SERIALIZED] = $conditions;


        if (isset($data[TemplateInterface::CATEGORY_IMAGE][0])) {
            $data[TemplateInterface::CATEGORY_IMAGE] = $data[TemplateInterface::CATEGORY_IMAGE][0]['name'];
        } else {
            $data[TemplateInterface::CATEGORY_IMAGE] = '';
        }

        if (!isset($data[TemplateInterface::DESCRIPTION_POSITION])) {
            $data[TemplateInterface::DESCRIPTION_POSITION] = TemplateInterface::DESCRIPTION_POSITION_DISABLED;
        }

        return $data;
    }

    protected function fillModelWithData(TemplateInterface $model, array $data): TemplateInterface
    {
        $model->setRuleType((int)$data[TemplateInterface::RULE_TYPE])
            ->setName($data[TemplateInterface::NAME])
            ->setIsActive((bool)$data[TemplateInterface::IS_ACTIVE])
            ->setSortOrder((int)$data[TemplateInterface::SORT_ORDER])
            ->setTitle($data[TemplateInterface::TITLE])
            ->setMetaTitle($data[TemplateInterface::META_TITLE])
            ->setMetaKeywords($data[TemplateInterface::META_KEYWORDS])
            ->setMetaDescription($data[TemplateInterface::META_DESCRIPTION])
            ->setDescription($data[TemplateInterface::DESCRIPTION])
            ->setShortDescription($data[TemplateInterface::SHORT_DESCRIPTION])
            ->setFullDescription($data[TemplateInterface::FULL_DESCRIPTION])
            ->setDescriptionPosition((int)$data[TemplateInterface::DESCRIPTION_POSITION])
            ->setDescriptionTemplate($data[TemplateInterface::DESCRIPTION_TEMPLATE])
            ->setCategoryDescription($data[TemplateInterface::CATEGORY_DESCRIPTION])
            ->setCategoryImage($data[TemplateInterface::CATEGORY_IMAGE])
            ->setStopRuleProcessing((bool)$data[TemplateInterface::STOP_RULE_PROCESSING])
            ->setApplyForChildCategories((bool)$data[TemplateInterface::APPLY_FOR_CHILD_CATEGORIES])
            ->setStoreIds($data[TemplateInterface::STORE_IDS])
            ->setConditionsSerialized($data[TemplateInterface::CONDITIONS_SERIALIZED])
            ->setApplyForHomepage((bool)$data[TemplateInterface::APPLY_FOR_HOMEPAGE]);

        return $model;
    }
}

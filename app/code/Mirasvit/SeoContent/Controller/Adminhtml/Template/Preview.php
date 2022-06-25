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


namespace Mirasvit\SeoContent\Controller\Adminhtml\Template;


use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\SeoContent\Service\ContentService;
use Mirasvit\SeoContent\Service\PreviewService;
use Mirasvit\SeoContent\Block\Adminhtml\Template\Preview as PreviewBlock;

class Preview extends Save
{
    private $layout;

    private $previewService;

    public function __construct(
        PreviewService $previewService,
        TemplateRepositoryInterface $templateRepository,
        Registry $registry,
        Context $context,
        ContentService $contentService
    ) {
        $this->layout         = $context->getView()->getLayout();
        $this->previewService = $previewService;

        parent::__construct($templateRepository, $registry, $context, $contentService);
    }

    public function execute()
    {
        $data    = [];
        $content = '';

        if ($post = $this->getRequest()->getPostValue('data')) {
            if ($this->getRequest()->getParam(TemplateInterface::ID) == 'new') {
                $this->getRequest()->setParams([TemplateInterface::ID => null]);
            }

            /** @var TemplateInterface $model */
            $model = $this->initModel();
            //we receive form values as query string
            parse_str($post, $data);

            $data[TemplateInterface::STORE_IDS] = explode(',', $data[TemplateInterface::STORE_IDS]);

            $data  = $this->filter($data, $model);
            $model = $this->fillModelWithData($model, $data);
        }

        $ids = [];
        $url = '';

        if ($param = $this->getRequest()->getPostValue('preview_param')) {
            $param = trim($param);

            if (strpos($param, 'http') === 0) {
                $url = $param;
            } else {
                $ids = explode(',', $this->getRequest()->getPostValue('preview_param'));
            }
        }

        $previewData = $this->previewService->getPreview($model, $ids, $url);
        $contentType = 'text/html';

        if ($data) {
            $content = $this->layout->createBlock(PreviewBlock::class)
                ->setTemplate('Mirasvit_SeoContent::template/preview.phtml')
                ->setSeoTemplate($model)
                ->setPreviewData($previewData)
                ->toHtml();
        }

        $response = $this->getResponse();

        return $response
            ->setHeader('Content-Type', $contentType)
            ->setBody($content);
    }

    public function _processUrlKeys()
    {
        return true;
    }
}

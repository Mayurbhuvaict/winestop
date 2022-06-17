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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;

class ImageUpload extends Action
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * ImageUpload constructor.
     * @param ImageUploader $imageUploader
     * @param Context $context
     */
    public function __construct(
        ImageUploader $imageUploader,
        Context $context
    ) {
        parent::__construct($context);

        $this->imageUploader = $imageUploader;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $result = $this->imageUploader->saveFileToTmpDir('category_image');
        } catch (\Exception $exception) {
            $result = [
                'error'     => $exception->getMessage(),
                'errorcode' => $exception->getCode(),
            ];
        }

        return $resultJson->setData($result);
    }
}

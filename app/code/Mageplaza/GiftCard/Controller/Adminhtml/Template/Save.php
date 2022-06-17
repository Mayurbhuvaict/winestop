<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Controller\Adminhtml\Template;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class Save
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Template
 */
class Save extends \Mageplaza\GiftCard\Controller\Adminhtml\Template
{
    /**
     * @var Template
     */
    protected $_templateHelper;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     * @param Template $templateHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        Template $templateHelper
    ) {
        $this->_templateHelper = $templateHelper;
        $this->mediaDirectory  = $templateHelper->getMediaDirectory();

        parent::__construct($context, $resultPageFactory, $templateFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws LocalizedException
     */
    public function execute()
    {
        $data       = $this->getRequest()->getPostValue();
        $templateId = (int) $this->getRequest()->getParam('id');
        if ($data) {
            $this->_uploadImages($data);

            // save data in session
            $this->_getSession()->setTemplateFormData($data);

            // init model and set data
            $template = $this->_initObject();
            if ($templateId && !$template->getId()) {
                $this->messageManager->addErrorMessage(__('This template does not exist.'));
                $this->_redirect('*/*/');

                return;
            }

            $template->addData($data);

            // try to save it
            try {
                $template->save();

                $this->messageManager->addSuccessMessage(__('The Template has been saved successfully.'));

                // clear previously saved data from session
                $this->_getSession()->setTemplateFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $template->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (LocalizedException $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving template.'));
            }
            // redirect to edit form
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param $data
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _uploadImages(&$data)
    {
        if (isset($data['background_image']['delete']) && $data['background_image']['delete']) {
            $data['background_image'] = '';
        } else {
            try {
                $uploader = $this->_objectManager->create(
                    Uploader::class,
                    ['fileId' => 'background_image']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);

                $image = $uploader->save($this->mediaDirectory->getAbsolutePath(Template::TEMPLATE_MEDIA_PATH));

                $data['background_image'] = Template::TEMPLATE_MEDIA_PATH . '/' . $image['file'];
            } catch (Exception $e) {
                $data['background_image'] = isset($data['background_image']['value'])
                    ? $data['background_image']['value'] : '';
            }
        }

        if (isset($data['images']) && count($data['images'])) {
            $data['images'] = Template::jsonEncode($this->processImagesGallery($data['images']));
        }

        return $this;
    }

    /**
     * @param $imageEntries
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function processImagesGallery($imageEntries)
    {
        foreach ($imageEntries as $key => &$image) {
            if (!isset($image['file']) || !$image['file']) {
                unset($imageEntries[$key]);
                continue;
            }

            $fileName = $image['file'];
            $pos      = strpos($fileName, '.tmp');

            if ((isset($image['removed']) && $image['removed'])) {
                /** Remove image */
                unset($imageEntries[$key]);

                if ($pos === false) {
                    $filePath = $this->_templateHelper->getMediaPath($image['file']);
                    $file     = $this->mediaDirectory->getRelativePath($filePath);
                    if ($this->mediaDirectory->isFile($file)) {
                        $this->mediaDirectory->delete($filePath);
                    }
                }
            } elseif ($pos !== false) {
                /** Move image from tmp folder */
                $fileName = substr($fileName, 0, $pos);
                $filePath = $this->_templateHelper->getTmpMediaPath($fileName);
                $file     = $this->mediaDirectory->getRelativePath($filePath);
                if (!$this->mediaDirectory->isFile($file)) {
                    unset($imageEntries[$key]);
                    continue;
                }

                $pathInfo = pathinfo($file);
                if (!isset($pathInfo['extension']) || !in_array(
                    strtolower($pathInfo['extension']),
                    ['jpg', 'jpeg', 'gif', 'png']
                )) {
                    unset($imageEntries[$key]);
                    continue;
                }

                $fileName       = Uploader::getCorrectFileName($pathInfo['basename']);
                $dispretionPath = Uploader::getDispretionPath($fileName);
                $fileName       = $dispretionPath . '/' . $fileName;

                $fileName        = $this->_templateHelper->getNotDuplicatedFilename($fileName, $dispretionPath);
                $destinationFile = $this->_templateHelper->getMediaPath($fileName);

                try {
                    $this->mediaDirectory->renameFile($file, $destinationFile);
                    $image['file'] = str_replace('\\', '/', $fileName);
                } catch (Exception $e) {
                    throw new LocalizedException(__('We couldn\'t move this file: %1.', $e->getMessage()));
                }
            }

            if (isset($image['removed'])) {
                unset($image['removed']);
            }
        }

        return array_values($imageEntries);
    }
}

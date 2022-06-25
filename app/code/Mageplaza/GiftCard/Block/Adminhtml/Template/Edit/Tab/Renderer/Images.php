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

namespace Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab\Renderer;

use Exception;
use Magento\Backend\Block\DataProviders\ImageUploadConfig;
use Magento\Backend\Block\DataProviders\UploadConfig;
use Magento\Backend\Block\Media\Uploader;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Helper\Template;

/**
 * Class Images
 * @package Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab\Renderer
 */
class Images extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'template/gallery.phtml';

    /**
     * @var Template
     */
    protected $_mediaConfig;

    /**
     * @var UploadConfig || \Magento\Backend\Block\DataProviders\ImageUploadConfig
     */
    private $imageUploadConfigDataProvider;

    /**
     * Images constructor.
     *
     * @param Context $context
     * @param Template $mediaConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Template $mediaConfig,
        array $data = []
    ) {
        $this->_mediaConfig = $mediaConfig;

        $objectManager = ObjectManager::getInstance();
        if ($this->_mediaConfig->versionCompare('2.2.8') &&
            $this->_mediaConfig->versionCompare('2.3', '<')
        ) {
            $this->imageUploadConfigDataProvider = $objectManager->get(UploadConfig::class);
        }
        if ($this->_mediaConfig->versionCompare('2.3')) {
            $this->imageUploadConfigDataProvider = $objectManager->get(ImageUploadConfig::class);
        }

        parent::__construct($context, $data);
    }

    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'uploader',
            Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->getUrl('mpgiftcard/template/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve uploader block
     *
     * @return bool|Uploader|AbstractBlock
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Retrieve uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * @return string
     */
    public function getImagesJson()
    {
        $value = $this->getElement()->getImages();
        if (is_array($value) && count($value)
        ) {
            $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $images   = $this->sortImagesByPosition($value);
            foreach ($images as $key => &$image) {
                $image['url'] = $this->_mediaConfig->getMediaUrl($image['file']);
                try {
                    $fileHandler   = $mediaDir->stat($this->_mediaConfig->getMediaPath($image['file']));
                    $image['size'] = $fileHandler['size'];
                } catch (Exception $e) {
                    $this->_logger->warning($e);
                    unset($images[$key]);
                }
            }

            return Data::jsonEncode($images);
        }

        return '[]';
    }

    /**
     * Sort images array by position key
     *
     * @param array $images
     *
     * @return array
     */
    private function sortImagesByPosition($images)
    {
        if (is_array($images)) {
            usort($images, function ($imageA, $imageB) {
                return ($imageA['position'] < $imageB['position']) ? -1 : 1;
            });
        }

        return $images;
    }

    /**
     * Get image types data
     *
     * @return array
     */
    public function getImageTypes()
    {
        return [
            'image' => [
                'code'  => 'images',
                'value' => $this->getElement()->getDataObject()->getImages(),
                'label' => 'Template Images',
                'scope' => 'Template Images',
                'name'  => 'template-images',
            ]
        ];
    }

    /**
     * Retrieve JSON data
     *
     * @return string
     */
    public function getImageTypesJson()
    {
        return Data::jsonEncode($this->getImageTypes());
    }
}

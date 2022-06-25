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



namespace Mirasvit\Seo\Plugin\Frontend\Catalog\Model\Product;

use Mirasvit\Seo\Model\Config\ImageConfig;
use Mirasvit\Seo\Api\Service\FriendlyImageUrlServiceInterface;

class FriendlyImageUrlPlugin
{
    /**
     * @var ImageConfig
     */
    private $imageConfig;

    /**
     * @var FriendlyImageUrlServiceInterface
     */
    private $friendlyImageUrlService;

    /**
     * FriendlyImageUrlPlugin constructor.
     *
     * @param ImageConfig                      $imageConfig
     * @param FriendlyImageUrlServiceInterface $friendlyImageUrlService
     */
    public function __construct(
        ImageConfig $imageConfig,
        FriendlyImageUrlServiceInterface $friendlyImageUrlService
    ) {
        $this->imageConfig             = $imageConfig;
        $this->friendlyImageUrlService = $friendlyImageUrlService;
    }

    /**
     * @param mixed $subject
     * @param null  $key
     * @param null  $value
     *
     * @return array
     */
    public function beforeSetData($subject, $key = null, $value = null)
    {
        if (!$key || !is_scalar($key)) {
            return [$key, $value];
        }

        if (in_array($key, ['image', 'small_image'])) {
            if ($this->imageConfig->isFriendlyUrlEnabled()) {
                \Magento\Framework\Profiler::start(__METHOD__ . "#getFriendlyImageName");
                $value = $this->friendlyImageUrlService->getFriendlyImageName($subject, $value);
                \Magento\Framework\Profiler::stop(__METHOD__ . "#getFriendlyImageName");
            }
        } elseif ($key === 'media_gallery') {
            \Magento\Framework\Profiler::start(__METHOD__ . "#updateGallery");
            $value = $this->updateGallery($subject, $value);
            \Magento\Framework\Profiler::stop(__METHOD__ . "#updateGallery");
        }

        return [$key, $value];
    }

    /**
     * @param mixed  $subject
     * @param string $value
     * @param null   $key
     *
     * @return string
     */
    public function afterGetData($subject, $value, $key = null)
    {
        if (!$key) {
            return $value;
        }

        if (in_array($key, ['image', 'small_image', 'swatch_image'])) {
            if ($this->imageConfig->isFriendlyUrlEnabled()) {
                \Magento\Framework\Profiler::start(__METHOD__ . "#getFriendlyImageName");
                $value = $this->friendlyImageUrlService->getFriendlyImageName($subject, $value);
                \Magento\Framework\Profiler::stop(__METHOD__ . "#getFriendlyImageName");
            }
        } elseif (in_array($key, ['image_label', 'small_image_label'])) {
            \Magento\Framework\Profiler::start(__METHOD__);
            if ($this->imageConfig->isFriendlyAltEnabled()) {
                $value = $this->friendlyImageUrlService->getFriendlyImageAlt($subject);
            }
            \Magento\Framework\Profiler::stop(__METHOD__);
        }

        return $value;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $value
     *
     * @return string|array
     */
    public function updateGallery($product, $value)
    {
        if (!is_array($value) || !key_exists('images', $value)) {
            return $value;
        }

        foreach ($value['images'] as $idx => $imageData) {
            if ($this->imageConfig->isFriendlyUrlEnabled()) {
                \Magento\Framework\Profiler::start(__METHOD__ . "#getFriendlyImageName");
                $value['images'][$idx]['file'] = $this->friendlyImageUrlService->getFriendlyImageName($product, $imageData['file']);
                \Magento\Framework\Profiler::stop(__METHOD__ . "#getFriendlyImageName");
            }

            if ($this->imageConfig->isFriendlyAltEnabled()) {
                \Magento\Framework\Profiler::start(__METHOD__ . "#getFriendlyImageAlt");
                $value['images'][$idx]['label'] = $this->friendlyImageUrlService->getFriendlyImageAlt($product);
                \Magento\Framework\Profiler::stop(__METHOD__ . "#getFriendlyImageAlt");
            }
        }

        return $value;
    }
}

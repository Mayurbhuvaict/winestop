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



namespace Mirasvit\Seo\Plugin\Frontend\Catalog\Block\Product\View\Gallery;

use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filter\FilterManager;
use Mirasvit\Seo\Model\Config\ImageConfig;
use Mirasvit\Seo\Service\TemplateEngineService;

class SetMainImagePlugin
{
    /**
     * @var ImageConfig
     */
    private $imageConfig;

    /**
     * @var TemplateEngineService
     */
    private $templateEngineService;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var MediaConfig
     */
    private $mediaConfig;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * SetMainImagePlugin constructor.
     * @param ImageConfig $imageConfig
     * @param TemplateEngineService $templateEngineService
     * @param FilterManager $filterManager
     * @param MediaConfig $mediaConfig
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ImageConfig $imageConfig,
        TemplateEngineService $templateEngineService,
        FilterManager $filterManager,
        MediaConfig $mediaConfig,
        DirectoryList $directoryList
    ) {
        $this->imageConfig           = $imageConfig;
        $this->templateEngineService = $templateEngineService;
        $this->filterManager         = $filterManager;
        $this->mediaConfig           = $mediaConfig;
        $this->directoryList         = $directoryList;
    }

    /**
     * @param mixed $subject
     * @param \Closure $closure
     * @param mixed $image
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function aroundIsMainImage($subject, \Closure $closure, $image)
    {
        if ($this->imageConfig->isFriendlyUrlEnabled()) {
            \Magento\Framework\Profiler::start(__METHOD__);
            $productImage = $this->getFriendlyImageName($subject->getProduct(), $subject->getProduct()->getImage());
            \Magento\Framework\Profiler::stop(__METHOD__);
            return $image->getFile() == $productImage;
        } else {
            return $closure($image);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $fileName
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getFriendlyImageName($product, $fileName)
    {
        $newFile = DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . $this->generateName($product, $fileName);

        $absPath = $this->directoryList->getPath('media')
            . DIRECTORY_SEPARATOR . $this->mediaConfig->getBaseMediaPath()
            . DIRECTORY_SEPARATOR . ltrim($fileName, DIRECTORY_SEPARATOR);

        $absNewPath = $this->directoryList->getPath('media')
            . DIRECTORY_SEPARATOR . $this->mediaConfig->getBaseMediaPath()
            . DIRECTORY_SEPARATOR . ltrim($newFile, DIRECTORY_SEPARATOR);

        try {
            if (file_exists($absPath) && !file_exists($absNewPath)) {
                mkdir(dirname($absNewPath), 0777, true);
                copy($absPath, $absNewPath);
            }
        } catch (\Exception $e) {
            return $fileName;
        }

        return $newFile;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $fileName
     *
     * @return string
     */
    private function generateName($product, $fileName)
    {
        $imageUrlTemplate = $this->imageConfig->getUrlTemplate();

        $label     = $this->templateEngineService->render($imageUrlTemplate, ['product' => $product]);
        $imageName = $this->filterManager->translitUrl($label);
        $suffix    = preg_replace('/(.*)(\\.)/', '.', $fileName);

        $imagePath = $product->getId() . substr(hash('sha256', $fileName), 4, 4);

        return $imagePath . '/' . $imageName . $suffix;
    }
}

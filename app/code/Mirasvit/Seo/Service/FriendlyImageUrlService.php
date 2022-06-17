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



namespace Mirasvit\Seo\Service;

use Mirasvit\Seo\Api\Service\FriendlyImageUrlServiceInterface;
use Mirasvit\Seo\Service\TemplateEngine\TemplateProcessor;
use Mirasvit\Seo\Model\Config\ImageConfig;
use Magento\Framework\Filter\FilterManager;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Mirasvit\Seo\Service\TemplateEngineService;

class FriendlyImageUrlService implements FriendlyImageUrlServiceInterface
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
     * @var array
     */
    private $cache = [];
    /**
     * @var array
     */
    private $cacheAlt = [];

    /**
     * FriendlyImageUrlService constructor.
     * @param ImageConfig $imageConfig
     * @param \Mirasvit\Seo\Service\TemplateEngineService $templateEngineService
     * @param FilterManager $filterManager
     * @param MediaConfig $mediaConfig
     * @param DirectoryList $directoryList
     */
    public function __construct(
        ImageConfig $imageConfig,
        TemplateEngineService $templateEngineService,
        FilterManager $filterManager,
        MediaConfig $mediaConfig, //can't use MediaConfigInterface, because of magento tests error in M2.1
        DirectoryList $directoryList
    ) {
        $this->imageConfig           = $imageConfig;
        $this->templateEngineService = $templateEngineService;
        $this->filterManager         = $filterManager;
        $this->mediaConfig           = $mediaConfig;
        $this->directoryList         = $directoryList;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $fileName
     * @return string
     */
    public function getFriendlyImageName($product, $fileName)
    {
        if (isset($this->cache[$product->getId()][$fileName])) {
            return $this->cache[$product->getId()][$fileName];
        } else {
            $this->cache[$product->getId()] = [];
        }
        
        // swatch images fix (processed already)
        if (strpos($fileName, DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR) !== false) {
            $this->cache[$product->getId()][$fileName] = $fileName;
            return $fileName;
        }

        \Magento\Framework\Profiler::start(__METHOD__);
        $newFile = DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . $this->generateName($product, $fileName);

        $absPath = $this->directoryList->getPath('media')
            . DIRECTORY_SEPARATOR . $this->mediaConfig->getBaseMediaPath()
            . DIRECTORY_SEPARATOR . ltrim($fileName, DIRECTORY_SEPARATOR);

        $absNewPath = $this->directoryList->getPath('media')
            . DIRECTORY_SEPARATOR . $this->mediaConfig->getBaseMediaPath()
            . DIRECTORY_SEPARATOR . ltrim($newFile, DIRECTORY_SEPARATOR);
        try {
            if (file_exists($absPath)) {
                if (!file_exists($absNewPath)) {
                    if (!is_dir(dirname($absNewPath))) {
                        mkdir(dirname($absNewPath), 0777, true);
                    }
                    copy($absPath, $absNewPath);
                }
            } else { // use old file name of the image does not exist (swatches issue)
                $newFile = $fileName;
            }
        } catch (\Exception $e) {
            $this->cache[$product->getId()][$fileName] = $fileName;
            return $fileName;
        }
        $this->cache[$product->getId()][$fileName] = $newFile;
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $newFile;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getFriendlyImageAlt($product)
    {
        if (isset($this->cacheAlt[$product->getId()])) {
            return $this->cacheAlt[$product->getId()];
        }

        \Magento\Framework\Profiler::start(__METHOD__);
        $template = $this->imageConfig->getAltTemplate();

        $res = $this->templateEngineService->render($template, ['product' => $product]);
        $this->cacheAlt[$product->getId()]  = $res;
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $res;
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

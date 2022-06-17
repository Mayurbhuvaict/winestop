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



namespace Mirasvit\Seo\Service\Image;

use Magento\Catalog\Block\Product\ImageBuilder;
use Mirasvit\Seo\Api\Service\Image\ImageServiceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class ImageService implements ImageServiceInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    /**
     * ImageService constructor.
     * @param ImageBuilder $imageBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ImageBuilder $imageBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->imageBuilder = $imageBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductBaseImageUrlByProduct($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryImageUrl($imageName)
    {
        $store = $this->storeManager->getStore();
        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
        . $this->getFilePath('catalog/category', $imageName);
    }

    /**
     * @param string $path
     * @param string $fileName
     * @return string
     */
    private function getFilePath($path, $fileName)
    {
        return rtrim($path, '/') . '/' . ltrim($fileName, '/');
    }
}

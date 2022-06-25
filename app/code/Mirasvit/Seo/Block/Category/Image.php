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



namespace Mirasvit\Seo\Block\Category;

use Magento\Catalog\Block\Category\View;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Api\Service\Image\ImageServiceInterface;
use Mirasvit\Seo\Helper\Data;

class Image extends View
{
    /**
     * @var Data
     */
    private $seoData;
    /**
     * @var ImageServiceInterface
     */
    private $imageService;

    /**
     * Image constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param Registry $registry
     * @param Category $categoryHelper
     * @param ImageServiceInterface $imageService
     * @param Data $seoData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Registry $registry,
        Category $categoryHelper,
        ImageServiceInterface $imageService,
        Data $seoData,
        array $data = []
    ) {
        $this->imageService = $imageService;
        $this->seoData      = $seoData;
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
    }


    /**
     * Return identifiers for produced content
     * @return array
     */
    public function getCategoryImageUrl()
    {
        return null;
    }
}

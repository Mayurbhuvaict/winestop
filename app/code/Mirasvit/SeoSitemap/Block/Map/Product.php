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



namespace Mirasvit\SeoSitemap\Block\Map;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Framework\View\Element\Template;
use Mirasvit\SeoSitemap\Model\Config;

class Product extends Template
{
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductStatus
     */
    protected $productStatus;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Template\Context
     */
    private $context;

    /**
     * Category constructor.
     *
     * @param Template\Context         $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductStatus            $productStatus
     * @param Config                   $config
     * @param array                    $data
     */
    public function __construct(
        Template\Context         $context,
        ProductCollectionFactory $productCollectionFactory,
        ProductStatus            $productStatus,
        Config                   $config,
        array $data = []
    ) {
        $this->context                  = $context;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus            = $productStatus;
        $this->config                   = $config;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return __('Products');
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'visibility',
                ['nin' => [\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]]
            )
            ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->addAttributeToSort('name', 'ASC')
            ->setStore($this->context->getStoreManager()->getStore())
            ->addStoreFilter($this->context->getStoreManager()->getStore()->getId());

        return $collection;
    }

    /**
     * @return mixed
     */
    public function isCapitalLettersEnabled()
    {
        return $this->config->isCapitalLettersEnabled();
    }

    /**
     * @return mixed
     */
    public function isShowProducts()
    {
        return $this->config->getIsShowProducts();
    }
}

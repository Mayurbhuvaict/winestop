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

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Mirasvit\Seo\Api\Service\StateServiceInterface;

class StateService implements StateServiceInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    public function __construct(
        Registry $registry,
        RequestInterface $request,
        LayerResolver $layerResolver
    ) {
        $this->registry      = $registry;
        $this->request       = $request;
        $this->layerResolver = $layerResolver;
    }

    /**
     * @return false|\Magento\Catalog\Model\Category|mixed
     */
    public function getCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return false|\Magento\Catalog\Model\Product|mixed
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCmsPage()
    {
        return $this->registry->registry('current_cms_page');
    }

    /**
     * @return bool|false|\Magento\Catalog\Model\Category|\Magento\Framework\DataObject|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFilters()
    {
        if (!$this->isNavigationPage() || !$this->getCategory()) {
            return null;
        }

        $category = $this->getCategory();

        $filters        = $this->layerResolver->get()->getState()->getFilters();
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $poductResource = $objectManager->get('Magento\Catalog\Model\ResourceModel\ProductFactory')->create();

        foreach ($filters as $filter) {
            if (method_exists($filter, 'getValueString') && $filter->getFilter()->getRequestVar() !== null) {
                try {
                    $category->setData($filter->getFilter()->getRequestVar(), $filter->getValueString());
                } catch (\Exception $e) {
                }
            } else {
                /** @var mixed $filter */
                if ($filter->isApplied()) {
                    $attribute = $poductResource->getAttribute($filter->getFilter()->getData('param_name'));
                    foreach ($filter->getAppliedOptions() as $key => $value) {
                        if ($attribute->usesSource()) {
                            $optionText = $attribute->getSource()->getOptionText($value);
                        } else {
                            $optionText = $value;
                        }
                        try {
                            $category->setData($filter->getFilter()->getData('param_name'), $optionText);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        return $category;
    }

    /**
     * @return bool
     */
    public function isCategoryPage()
    {
        return $this->getCategory() && $this->request->getFullActionName() == 'catalog_category_view';
    }

    /**
     * @return bool
     */
    public function isNavigationPage()
    {
        try {
            $filters = $this->layerResolver->get()->getState()->getFilters();
        } catch (\Exception $e) {
            return false;
        }

        return $this->isCategoryPage() && count($filters) > 0;
    }

    /**
     * @return bool
     */
    public function isProductPage()
    {
        return $this->getProduct() && $this->request->getFullActionName() == 'catalog_product_view';
    }

    /**
     * @return bool
     */
    public function isCmsPage()
    {
        return $this->isHomePage() || $this->request->getFullActionName() == 'cms_page_view';
    }

    /**
     * @return bool
     */
    public function isHomePage()
    {
        if ($this->request->getFullActionName() == 'cms_index_index') {
            return true;
        }

        return false;
    }
}

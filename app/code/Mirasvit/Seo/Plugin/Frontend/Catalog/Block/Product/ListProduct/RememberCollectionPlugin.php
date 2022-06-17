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



namespace Mirasvit\Seo\Plugin\Frontend\Catalog\Block\Product\ListProduct;

use Magento\Framework\Registry;
use Mirasvit\Seo\Api\Config\CurrentPageProductsInterface;

class RememberCollectionPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * RememberCollectionPlugin constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param mixed $subject
     * @param mixed $collection
     * @return mixed
     */
    public function afterGetLoadedProductCollection($subject, $collection)
    {
        $this->registry->register(CurrentPageProductsInterface::PRODUCT_COLLECTION, $collection, true);

        return $collection;
    }
}

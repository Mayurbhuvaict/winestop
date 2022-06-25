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



namespace Mirasvit\Seo\Service\UrlTemplate;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;

class ProductUrlRegenerateService
{
    /**
     * @var ProductUrlRewriteGenerator
     */
    private $productUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * ProductUrlRegenerateService constructor.
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param ProductCollectionFactory $productCollectionFactory
     */
    public function __construct(
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist                 = $urlPersist;
        $this->productCollectionFactory   = $productCollectionFactory;
    }

    /**
     * @return \Generator
     * @throws \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException
     */
    public function restore()
    {
        $collection = $this->getProductCollection();

        $pageCount   = $collection->getLastPageNumber();
        $currentPage = 1;

        while ($currentPage <= $pageCount) {
            yield sprintf('Page %s of %s', $currentPage, $pageCount);

            $collection->clear();
            $collection->setCurPage($currentPage);

            foreach ($collection as $product) {
                $product->setStoreId(0);
                try {
                    if (!$product->getUrlKey()) {
                        $product->setUrlKey($product->getId() . '-' . $product->getName());
                        $product->setStoreId(0)->save();
                    }
                    $this->urlPersist->replace($this->productUrlRewriteGenerator->generate($product));
                } catch (\Exception $e) {
                }

            }

            $currentPage++;
        }
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setStore(0)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path')
            ->addAttributeToSelect('status')
            ->addAttributeToFilter('status', 1)
            ->setPageSize(100);

        return $collection;
    }
}

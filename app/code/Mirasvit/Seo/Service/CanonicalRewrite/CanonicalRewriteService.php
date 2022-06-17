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



namespace Mirasvit\Seo\Service\CanonicalRewrite;

use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Mirasvit\Seo\Api\Service\CanonicalRewrite\CanonicalRewriteServiceInterface;
use Mirasvit\Seo\Api\Service\StateServiceInterface;

class CanonicalRewriteService implements CanonicalRewriteServiceInterface
{
    /**
     * @var int
     */
    protected $productId;

    /**
     * @var int
     */
    protected $categoryId;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    private $resourceIterator;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $productFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var \Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface
     */
    private $canonicalRewriteRepository;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlInterface;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var \Mirasvit\Seo\Api\Service\StateServiceInterface
     */
    private $stateService;

    /**
     * CanonicalRewriteService constructor.
     * @param \Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface $canonicalRewriteRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Mirasvit\Seo\Api\Service\StateServiceInterface $stateService
     */
    public function __construct(
        \Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface $canonicalRewriteRepository,
        \Mirasvit\Seo\Api\Service\StateServiceInterface $stateService,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->canonicalRewriteRepository = $canonicalRewriteRepository;
        $this->stateService               = $stateService;
        $this->storeManager               = $storeManager;
        $this->registry                   = $registry;
        $this->productFactory             = $productFactory;
        $this->categoryFactory            = $categoryFactory;
        $this->productCollectionFactory   = $productCollectionFactory;
        $this->categoryCollectionFactory  = $categoryCollectionFactory;
        $this->resourceIterator           = $resourceIterator;
        $this->urlInterface               = $urlInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalRewriteRule()
    {
        $productId            = false;
        $categoryId           = false;
        $canonicalRewriteRule = false;

        if ($this->registry->registry('current_product')) {
            $productId = $this->registry->registry('current_product')->getId();
        } elseif ($this->registry->registry('current_category')) {
            $categoryId = $this->registry->registry('current_category')->getId();
        }

        $collection = $this->canonicalRewriteRepository->getCollection()
            ->addStoreFilter($this->storeManager->getStore())
            ->addActiveFilter()
            ->addSortOrder();

        $uri = $this->urlInterface->getCurrentUrl();

        foreach ($collection as $item) {
            $rule = $this->getRuleById($item->getId());
            $expression = $item->getRegExpression();
            $match = 0;
            $productApplied = 0;
            $categoryApplied = 0;

            if (!empty($expression)) {
                try {
                    $match = preg_match($expression, $uri);
                } catch (\Exception $e) {
                }
            }

            if ($productId) {
                if ($this->isProductApplied($productId, $rule)) {
                    $productApplied = 1;
                }
            }

            if ($categoryId) {
                if ($this->isCategoryApplied($categoryId, $rule)) {
                    $categoryApplied = 1;
                }
            }

            if ($this->isRewriteForHomePage($expression)) {
                $canonicalRewriteRule = $item;
            }

            if (!empty($expression)) {
                if (!empty($rule->getConditions()->getData('conditions'))) {
                    if ($match && ($productApplied || $categoryApplied)) {
                        $canonicalRewriteRule = $item;
                        break;
                    }
                } else {
                    if ($match) {
                        $canonicalRewriteRule = $item;
                        break;
                    }
                }
            } else {
                if ($productApplied || $categoryApplied) {
                    $canonicalRewriteRule = $item;
                    break;
                }
            }
        }

        return $canonicalRewriteRule;
    }

    /**
     * @param int $ruleId
     *
     * @return \Mirasvit\Seo\Model\CanonicalRewrite
     */
    public function getRuleById($ruleId)
    {
        $rule = $this->canonicalRewriteRepository->getCollection()
            ->addFieldToFilter(CanonicalRewriteInterface::ID, $ruleId)
            ->getFirstItem();
        $rule = $rule->load($rule->getId());

        return $rule;
    }

    /**
     * @param string                              $productId
     * @param \Mirasvit\Seo\Model\CanonicalRewrite $rule
     *
     * @return bool
     */
    public function isProductApplied($productId, $rule)
    {
        if ($this->productId === null) {
            /** @var mixed $rule */
            $rule->setCollectedAttributes([]);
            $productCollection = $this->productCollectionFactory->create()->addFieldToFilter(
                'entity_id',
                $productId
            );

            $rule->getConditions()->collectValidatedAttributes($productCollection);

            $this->resourceIterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                [
                    'attributes' => $rule->getCollectedAttributes(),
                    'product'    => $this->productFactory->create(),
                    'rule'       => $rule,
                ]
            );
        }

        if ($this->productId) {
            return true;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        if (!empty($args['rule']->getConditions()->getData('conditions'))) {
            if ($args['rule']->getConditions()->validate($product)) {
                $this->productId = $product->getId();
            }
        } else {
            return false;
        }
    }

    /**
     * @param string                              $categoryId
     * @param \Mirasvit\Seo\Model\CanonicalRewrite $rule
     *
     * @return bool
     */
    public function isCategoryApplied($categoryId, $rule)
    {
        if ($this->categoryId === null) {
            $rule->setCollectedAttributes([]);
            $categoryCollection = $this->categoryCollectionFactory->create()->addFieldToFilter(
                'entity_id',
                $categoryId
            );

            $rule->getConditions()->collectValidatedAttributes($categoryCollection);

            $this->resourceIterator->walk(
                $categoryCollection->getSelect(),
                [[$this, 'callbackValidateCategory']],
                [
                    'attributes' => $rule->getCollectedAttributes(),
                    'category'   => $this->categoryFactory->create(),
                    'rule'       => $rule,
                ]
            );
        }

        if ($this->categoryId) {
            return true;
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    public function callbackValidateCategory($args)
    {
        $category = clone $args['category'];
        $category->setData($args['row']);
        if (!empty($args['rule']->getConditions()->getData('conditions'))) {
            if ($args['rule']->getConditions()->validate($category)) {
                $this->categoryId = $category->getId();
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $expression
     *
     * @return bool
     */
    private function isRewriteForHomePage($expression) {
        if ($this->stateService->isHomePage() && $expression == '/') {
            return true;
        }
    }
}

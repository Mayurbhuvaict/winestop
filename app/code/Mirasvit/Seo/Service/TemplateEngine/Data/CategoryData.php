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


declare(strict_types=1);

namespace Mirasvit\Seo\Service\TemplateEngine\Data;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class CategoryData extends AbstractData
{
    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $category;

    private $registry;

    private $storeManager;

    public function __construct(
        Registry $registry,
        StoreManagerInterface $storeManager
    ) {
        $this->registry     = $registry;
        $this->storeManager = $storeManager;

        parent::__construct();
    }

    public function getTitle(): string
    {
        return (string)__('Category Data');
    }

    public function getVariables(): array
    {
        return [
            'name',
            'url',
            'page_title',
            'parent_name',
            'parent_name_[level]',
            'parent_url',
        ];
    }

    public function setCategory(CategoryInterface $category): AbstractData
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): ?CategoryInterface
    {
        if (!$this->category) {
            return $this->registry->registry('current_category') ?: null;
        }

        return $this->category;
    }

    public function getValue(string $attribute, array $additionalData = []): ?string
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->getCategory();

        if (!$category) {
            return null;
        }

        switch ($attribute) {
            case 'page_title':
                return (string)$category->getMetaTitle();

            case 'parent_name':
            case 'parent_name_1':
                $parent = $this->getParentCategory($category, 1);

                return $parent ? (string)$parent->getName() : null;

            case 'parent_name_2':
                $parent = $this->getParentCategory($category, 2);

                return $parent ? (string)$parent->getName() : null;

            case 'parent_name_3':
                $parent = $this->getParentCategory($category, 3);

                return $parent ? (string)$parent->getName() : null;

            case 'parent_url':
                $parent = $this->getParentCategory($category);

                return $parent ? (string)$parent->getUrl() : null;
        }

        return $category->getDataUsingMethod($attribute) ?: null;
    }

    private function getParentCategory(CategoryInterface $category, int $level = 1): ?CategoryInterface
    {
        if (!$category) {
            return null;
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        /** @var \Magento\Catalog\Model\Category $parent */
        try {
            $parent = $category->getParentCategory();
        } catch (\Exception $e) {
            $parent = null;
        }

        if (!$parent) {
            return null;
        }

        if ($store->getRootCategoryId() == $parent->getId()) {
            return null;
        }

        if ($level <= 1) {
            return $parent;
        } else {
            return $this->getParentCategory($parent, $level - 1);
        }
    }
}

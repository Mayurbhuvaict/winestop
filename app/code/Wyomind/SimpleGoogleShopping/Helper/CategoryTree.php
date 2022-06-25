<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Helper;

class CategoryTree
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory|null
     */
    protected $_categoryFactory = null;
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_categoryFactory = $categoryFactory;
    }
    public function getTree()
    {
        $collection = $this->_categoryFactory->create()->addAttributeToSelect('name');
        $tree = [];
        foreach ($collection as $cat) {
            if (!isset($tree[$cat->getId()])) {
                $tree[$cat->getId()] = ['id' => $cat->getId(), 'text' => $cat->getName(), 'children' => []];
            } else {
                $tree[$cat->getId()]['id'] = $cat->getId();
                $tree[$cat->getId()]['text'] = $cat->getName();
            }
            if ($cat->getParentId() != 0) {
                if (isset($tree[$cat->getParentId()]['children'])) {
                    array_unshift($tree[$cat->getParentId()]['children'], $cat->getId());
                } else {
                    $tree[$cat->getParentId()]['children'] = [$cat->getId()];
                }
            }
        }
        return $tree;
    }
}
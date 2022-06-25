<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Block\Adminhtml\Feeds\Edit\Tab;

/**
 * Categories tab
 */
class Categories extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * @return string
     */
    public function getFeedTaxonomy()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingFeedTaxonomy();
    }
    /**
     * @return int
     */
    public function getCategoryFilter()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingCategoryFilter();
    }
    /**
     * @return int
     */
    public function getCategoryType()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingCategoryType();
    }
    /**
     * @return string
     */
    public function getSGSCategories()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        return $model->getSimplegoogleshoppingCategories();
    }
    /**
     * @param string $directory
     * @return array
     */
    public function dirFiles($directory)
    {
        $dir = dir($directory);
        // Open Directory
        // Reads Directory
        while (false !== ($file = $dir->read())) {
            $extension = substr($file, strrpos($file, '.'));
            // Gets the File Extension
            if ($extension == '.txt') {
                // Extensions Allowed
                // Store in Array
                $filesAll[$file] = $file;
            }
        }
        // Close Directory
        $dir->close();
        // Sorts the Array
        asort($filesAll);
        return $filesAll;
    }
    /**
     * @return array
     */
    public function getAvailableTaxonomies()
    {
        $controllerModule = $this->getRequest()->getControllerModule();
        $directory = $this->directoryReader->getModuleDir('', $controllerModule) . '/data/Google/Taxonomies/';
        if (file_exists($directory)) {
            return $this->dirFiles($directory);
        } else {
            return [];
        }
    }
    /**
     * @see Magento\Catalog\Block\Adminhtml\Category\Tree
     * @return array
     */
    public function getCategoriesJson()
    {
        return $this->tree->getTree();
    }
    public function getJsonTree()
    {
        $treeCategories = $this->tree->getTree();
        return json_encode($treeCategories);
    }
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('data_feed');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setValues($model->getData());
        $this->setForm($form);
        $this->setTemplate('edit/categories.phtml');
        return parent::_prepareForm();
    }
    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_coreRegistry->registry('data_feed')->getStoreId();
    }
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategories()
    {
        $tmp = $this->_categoryCollection->create();
        return $tmp->setStoreId($this->getStoreId())->addAttributeToSelect(['name'])->addAttributeToSort('path', 'ASC');
    }
    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Categories');
    }
    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Categories');
    }
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
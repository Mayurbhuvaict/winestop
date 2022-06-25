<?php

namespace Magecomp\Extrafee\Plugin\Block;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;
use Meetanshi\ShippingRates\Helper\Data;
use Magecomp\Extrafee\Model\CategoryFactory;

class GeneralModify
{
    public $categoryFactory;
    public $categoryOption;
    public $registry;

    public function __construct(
        CategoryFactory $categoryFactory,
        Registry $registry
    ){
        $this->categoryFactory = $categoryFactory;
        $this->registry = $registry;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function aroundGetFormHtml(
        \Meetanshi\ShippingRates\Block\Adminhtml\Method\Edit\Tab\General $subject,
        \Closure $proceed
    )
    {
        $form = $subject->getForm();
        if (is_object($form)) {
            $fieldset = $form->addFieldset('category', ['legend' => __('Category')]);
            $fieldset->addField(
                'category_id',
                'select',
                [
                    'name' => 'category_id',
                    'label' => __('Category'),
                    'id' => 'category',
                    'title' => __('Category'),
                    'required' => false,
                    'note' => 'Select Category',
                    'options' => $this->getCategoriesOption(),
                ]
            );

            $form->setValues($this->registry->registry('shippingrates_method')->getData());

            $subject->setForm($form);
        }

        return $proceed();
    }

    public function getCategoriesOption(){
        if(!$this->categoryOption){
            $collection = $this->categoryFactory->create()->getCollection();
            foreach ($collection as $key => $_row) {
                $this->categoryOption[$_row->getId()] = $_row->getCategoryName();
            }
        }
        return $this->categoryOption;
    }
}

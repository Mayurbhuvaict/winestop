<?php
/**
 * FAQ configuration file.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/shop/media/extension_doc/license_agreement.pdf
*/
namespace Rootways\Faq\Model\Config;

class Category implements \Magento\Framework\Option\ArrayInterface
{
    protected $_faqCategory;

    public function __construct(
        \Rootways\Faq\Model\Faqcat $faqCat
    ) {
        $this->_faqCategory = $faqCat;
    }
	
    protected function getCategoriesActive()
    {
        return $this->_faqCategory->getCollection()
        ->addFieldToFilter('is_active', '1')
        ->load()
        ->getData();
    }
	
    protected function getAllCategories()
    {
        return $this->_faqCategory->getCollection()->load()->getData();
    }
	
    public function toOptionArray()
    {
        $model = $this->_faqCategory->getCollection()->load()->getData();
        $results = [];
        $results[] = [
            'value' => '0',
            'label' => 'All Categories'
        ];
        foreach ($model as $value) {
            $results[] = [
                'value' => $value['cat_id'],
                'label' => $value['name']
            ];
        }
        return $results;
    }
	
    public function getCategoryOptions()
    {
        $model = $this->_faqCategory->getCollection()
        				->addFieldToFilter('status', '1')
        				->load()
        				->getData();

		$_result = array();
        $_result[] = array('value' => '', 'label' => '-- Select a category --');
        foreach ($model as $value) {
			$data = array(
                	'value' => $value['cat_id'],
                	'label' => $value['name']
				);
			
            $_result[] = $data;
        }
        return $_result;
    }
}

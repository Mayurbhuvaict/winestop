<?php
namespace Meigee\ProductWidget\Model\Config\Source;
 
class Categories implements \Magento\Framework\Option\ArrayInterface
{
	
	public function toOptionArray()
    {
        $collection = $this->_getCategoriesCollection();
        $collection->addAttributeToSelect('name');
        $options = [];
        foreach ($collection as $category) {
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }
        return $options;
    }
    /**
     * Get categories collection
     *
     * @return Collection
     */
    protected function _getCategoriesCollection()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();   
		$collectionFactory = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory'); 
		$this->_collectionFactory = $collectionFactory;
        return $this->_collectionFactory->create();
    }
}
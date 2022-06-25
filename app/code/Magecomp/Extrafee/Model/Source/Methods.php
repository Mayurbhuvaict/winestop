<?php 
namespace Magecomp\Extrafee\Model\Source;

use Meetanshi\ShippingRates\Model\ResourceModel\Method\CollectionFactory;

class Methods implements \Magento\Framework\Option\ArrayInterface
{
    protected $collectionFactory;
    protected $_options;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }
 
    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('main_table.is_active', 1)
                ->setOrder('pos');

            $resource = $collection->getResource();
            $shippingCategory = $resource->getTable('shippingrate_category');
            $collection->getSelect()->join(['sc'=>$shippingCategory],'main_table.category_id=sc.id',['category_name','position','cat_id'=>'sc.id'])->order('position ASC');
            $options = [];
            
            foreach ($collection as $key => $_row) {
                $options[$_row->getCatId()]['label']=$_row->getCategoryName();
                $options[$_row->getCatId()]['value'][]=[
                    'value'=>$_row->getId(),
                    'label'=>$_row->getName()
                ];
            }

            $this->_options = $options;
        }
        return $this->_options;
    }
}
?>
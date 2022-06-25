<?php
namespace Winestop\Shopby\Model;
 
use Winestop\Shopby\Model\ResourceModel\Type\CollectionFactory;
 
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $_loadedData;
    protected $storeManager;
   
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $typeCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $typeCollectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
 
   
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        //$this->_loadedData = array();

        foreach ($items as $types) {
            $image = [];
            $data = $types->getData();
            if (array_key_exists('image', $data) && isset($data['image'])) {
                $currentStore = $this->storeManager->getStore();
                $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $image[0]['name'] = $data['image'];
                $image[0]['url'] = $mediaUrl . $data['image'];
            }
            $data['image'] = $image;
            //echo "<pre>"; print_r($data); exit;
            $this->_loadedData[$types->getId()] = $data;
        }
        return $this->_loadedData;
        //return [];
    }
}
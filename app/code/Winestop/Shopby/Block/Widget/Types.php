<?php 
namespace Winestop\Shopby\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;  
 
class Types extends Template implements BlockInterface {

    protected $_template = "widget/type_collection.phtml";
    protected $modelFactory;
    protected $storeManager;
    public function __construct(
        Template\Context $context,
        \Winestop\Shopby\Model\ResourceModel\Type\CollectionFactory $modelFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->modelFactory = $modelFactory;
        $this->storeManager = $storeManager;
        $this->_isScopePrivate = true;
    }

    public function getCollection(){
        $collection = $this->modelFactory->create();
        return $collection;     
    }
    
    public function getMediaUrl(){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

}
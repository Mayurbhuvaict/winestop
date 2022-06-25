<?php 
namespace Winestop\Shopby\Block;

use Magento\Framework\View\Element\Template;

class Types extends Template
{    
    protected $modelFactory;
    public function __construct(
        Template\Context $context,
        \Winestop\Shopby\Model\ResourceModel\Type\CollectionFactory $modelFactory,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->modelFactory = $modelFactory;
        $this->_isScopePrivate = true;
    }

    public function getCollection(){
        $collection = $this->modelFactory->create();
        return $collection;     
    }

}
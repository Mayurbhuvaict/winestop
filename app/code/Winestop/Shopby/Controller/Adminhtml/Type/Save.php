<?php

namespace Winestop\Shopby\Controller\Adminhtml\Type;

use Magento\Framework\Exception\LocalizedException;
use Winestop\Shopby\Model\TypeFactory;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;
    protected $collectionFactory;
    protected $request;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Winestop\Shopby\Model\ResourceModel\Type\CollectionFactory $collectionFactory, 
        \Magento\Framework\App\Request\Http $request,
        TypeFactory $type
    ) {
        $this->_type = $type;
        $this->dataPersistor = $dataPersistor;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        parent::__construct($context);
    }

    public function execute()
    {       
        $TypeData = $this->getRequest()->getParams();
      // $param =  $this->getRequest()->getParam('id');
       //print_r($TypeData); exit;
         // echo "<pre>"; print_r($TypeData); exit;
        if($TypeData)
        {
            if (isset($TypeData['image'][0]['name']) && isset($TypeData['image'][0]['tmp_name'])) {
                $TypeData['image'] =$TypeData['image'][0]['name'];
                //$this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()->get('Winestop\Shopby\ImageUploader');
                //print_r($path); exit;
                //$this->imageUploader->moveFileFromTmp($TypeData['image']);
            } elseif (isset($TypeData['image'][0]['image']) && !isset($TypeData['image'][0]['tmp_name'])) {
                $TypeData['image'] = $TypeData['image'][0]['image'];
            } else {
                $TypeData['image'] = null;
            }
            try {
                $types = $TypeData['type'];
                
                $collection = $this->collectionFactory->create()->addFieldToFilter('type',array('eq' =>$types));
                foreach($collection->getData() as $collect){
                $exisingValue = $collect['type'];
                $exisingId = $collect['type_id'];
            } 
                //$id = $TypeData['type_id'];
                if((isset($collect['type_id'])) && ($collect['type_id'] != $TypeData['type_id']) && isset($exisingValue)){
                    $this->messageManager->addError(__('Image is already assigned to the selected type.'));
                }
                else {
                    $rowData = $this->_type->create();
                    $rowData->setData($TypeData);
                    $rowData->save();
                    $this->messageManager->addSuccess(__('You saved the item.'));
                }
            }catch (\Exception $e) {
                $this->messageManager->addError(__('Image is already assigned to the selected type.'));
                $this->_redirect('shopby/type/add');
                return;
            }
        }
        $this->_redirect('shopby/type/index');
        // try {
        //         $data = $this->getRequest()->getPostValue();
        //         $data = $this->_filterFoodData($data);
        //         //$model = $this->_objectManager->create('Winestop\Shopby\Model\Type');
        //         $types = $data['type'];
        //         $collection = $this->collectionFactory->create()->addFieldToFilter('type',array('eq' =>$types));
        //         foreach($collection->getData() as $collect){
        //            $exisingValue = $collect['type'];
        //         }
        //         if(isset($exisingValue)){
        //             $this->messageManager->addError(__('Image is already assigned to the selected type.'));
        //         }
        //         else {
        //             $rowData = $this->_type->create();
        //             $rowData->setData($data);
        //             $rowData->save();
        //             $this->messageManager->addSuccess(__('You saved the item.'));
        //         }
        //     }catch (\Exception $e) {
        //         $this->messageManager->addError(__('Something went wrong while saving the item data. Please review the error log.'));
        //         $this->_redirect('shopby/type/add');
        //         return;
        //     }
            
        // $this->_redirect('shopby/type/index');
    }

    public function _filterFoodData(array $rawData)
    {
        $data = $rawData;
        if (isset($data['image'][0]['name'])) {
            $data['image'] = $data['image'][0]['name'];
        } else {
            $data['image'] = null;
        }
        return $data;
    }
}
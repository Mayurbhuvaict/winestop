<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Winestop\Testimonials\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class hometestimonials extends Template 
{
        private $_modelFactory;
       public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context,
        \Winestop\Testimonials\Model\TestimonialsFactory $modelFactory,
       
        array $data = array()
    ) {
     
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        parent::__construct($context, $data);
    }

    public function getCollection(){    

    return $this->_modelFactory->create()->getCollection();

    }
    public function getImageBAse()
    {
         $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );

         return $mediaUrl;
    }
      public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }   

  }

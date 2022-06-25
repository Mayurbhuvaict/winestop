<?php
/**
 * Block for get collection of FAQ.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Block;

class Faq extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Rootways\Faq\Model\FaqFactory
     */
	protected $faq;
    
    /**
     * @var \Rootways\Faq\Helper\Data
     */
	protected $helper;
    
    /**
     * @var \Rootways\Faq\Model\FaqcatFactory
     */
	protected $category;
    
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Rootways\Faq\Model\FaqFactory $faq
     * @param \Rootways\Faq\Helper\Data $helper
     * @param \Rootways\Faq\Model\FaqcatFactory $category
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
	public function __construct (
        \Magento\Framework\View\Element\Template\Context $context,
		\Rootways\Faq\Model\FaqFactory $faq,
		\Rootways\Faq\Helper\Data $helper,
        \Rootways\Faq\Model\FaqcatFactory $category,
        \Zend_Filter_Interface $templateProcessor,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
		$this->_faq = $faq;
        $this->_category = $category;
		$this->_helper = $helper;
        $this->templateProcessor = $templateProcessor;
        $this->resource = $resource;
        parent::__construct($context);
    }
    
    public function filterOutputHtml($string)
    {
        return $this->templateProcessor->filter($string);
    }
	
	public function getCollection() {
		$pagesize = $this->_helper->perpagefaq();
		if($pagesize == ''){ 
			$pagesize = 10;
		}
		
    	 $collection = $this->_faq->create()->getCollection()
			->addFieldToFilter('status', '1')
			 ->addFieldToFilter('ismostfrequently', 1)
			 ->setPageSize($pagesize)
			->setCurPage(1);
        
		$storeId = $this->_storeManager->getStore()->getStoreId();
        $connection = $this->resource->getConnection();
        $storeTable = $connection->getTableName('rootways_faq_store');
		$collection->getSelect()->join(['r_store' => $storeTable], 'main_table.faq_id = r_store.faq_id')
			 ->where('r_store.store_id = '.$storeId . ' OR r_store.store_id = 0');
		
		$collection->setOrder('sortorder','ASC');
		return $collection;
	}
	
	public function getCategoryCollection() {
    	$collection = $this->_category->create()->getCollection()
				->addFieldToFilter('status', '1');
		
        $storeId = $this->_storeManager->getStore()->getStoreId();
        $connection = $this->resource->getConnection();
        $storeTable = $connection->getTableName('rootways_faq_cat_store');
		$collection->getSelect()->join(['r_store' => $storeTable], 'main_table.cat_id = r_store.cat_id')
			 ->where('r_store.store_id = '.$storeId . ' OR r_store.store_id = 0');
        
		$collection->setOrder('sortorder','ASC');
		return $collection;
	}
	
	public function AjaxPagination()
	{
		$pagesize = $this->_helper->perpagefaq();
		if ( $pagesize == '' ) { 
			$pagesize = 10;
		}
		$collection = $this->_faq->create()->getCollection()
			 		->addFieldToFilter('status', '1')
					->addFieldToFilter('ismostfrequently', 1);
		
		$storeId = $this->_storeManager->getStore()->getStoreId();
        $connection  = $this->resource->getConnection();
        $storeTable   = $connection->getTableName('rootways_faq_store');
		$collection->getSelect()->join(['r_store' => $storeTable], 'main_table.faq_id = r_store.faq_id')
			 ->where('r_store.store_id = '.$storeId . ' OR r_store.store_id = 0');				

		$cnt = count($collection);
		$pageHtml = '';
		if($cnt > $pagesize):
			$total_pages = ceil($cnt/$pagesize);
					
			$i = 1;
			$pageHtml .= '<div class="faq-right_title"><div class="pagenav">';
			while($i <= $total_pages) {
				if($i==1){$act = 'active';}else{ $act=''; }
				$pageHtml .= '<a class="faqpagination '.$act.'" id="page_'.$i.'">'.$i.'</a>';
				$i++;
			}
			$pageHtml .= '</div></div>';
		endif;
		
		return $pageHtml;
	}
}

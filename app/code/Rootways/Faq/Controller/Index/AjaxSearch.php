<?php
/**
 * FAQ Ajax Search Controller for display FAQ on front-end.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Controller\Index;

use Magento\Framework\Controller\Result\JsonFactory;

class AjaxSearch extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory $resultJsonFactory
     */
    private $resultJsonFactory;
    
    /**
     * @var \Rootways\Faq\Helper\Data
     */
	protected $helper;
    
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Rootways\Faq\Helper\Data $helper
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        \Rootways\Faq\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_helper = $helper;
        $this->resource = $resource;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }
 
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
		$pagesize = $this->_helper->perpagefaq();
		if ($pagesize == '') {
			$pagesize = 10;
		}
		$search_term = $this->getRequest()->getParam('search_term');
		$page_num = $this->getRequest()->getParam('page_num');
		
		$faq_model = $this->_objectManager->create('Rootways\Faq\Model\Faq');
		$collection = $faq_model->getCollection()
            ->addFieldToFilter('status', '1')
            ->addFieldToFilter(array('description', 'title'),
                               array(
                                   array('like'=>'%'.$search_term.'%'),
                                   array('like'=>'%'.$search_term.'%')
                               ))
            ->setPageSize($pagesize)
            ->setCurPage($page_num);
		
		$storeId = $this->_helper->getStoreId();
        $connection  = $this->resource->getConnection();
        $storeTable   = $connection->getTableName('rootways_faq_store');
		$collection->getSelect()->join(['r_store' => $storeTable], 'main_table.faq_id = r_store.faq_id')
			 ->where('r_store.store_id = '.$storeId . ' OR r_store.store_id = 0');
		$collection->setOrder('sortorder','ASC');
		
		$faqhtml = '';
		if (count($collection)) {
            $blockInstance = $this->_objectManager->get('Rootways\Faq\Block\Faq');
			$i = 1; 
			foreach ($collection as $data) {
				$faqhtml .= '<div class="faq_list">';
				$faqhtml .= '<a id="qus_id_'.$i.'" onClick="FaqqusTog(this.id)" class="faq-question">'.$data->getTitle().'</a>';
				$faqhtml .= '<div id="ans_id_'.$i.'" class="faq-text-content">'.$blockInstance->filterOutputHtml($data->getDescription()).'</div>';
				$faqhtml .= '</div>';
				$i++; 
			}
		} else {
			$faqhtml .= ('There is no FAQ found for this search term.');
		}
		
		$cnt = $collection->getSize();
		if ($cnt > $pagesize) {
			$total_pages = ceil($cnt/$pagesize);
		
			$i = 1;
			$faqhtml .= '<div class="faq-right_title"><div class="pagenav">';
			while($i <= $total_pages) {
				if($i == $page_num){ $act='active'; }
				elseif($page_num == '' && $i == 1){ $act='active';}
				else{ $act=''; }
				$faqhtml .= '<a onclick="PageChangeSearch(this.innerHTML)" value="'.$i.'" class="faqpagination '.$act.'" id="page_'.$i.'">'.$i.'</a>';
				$i++;
			}
			$faqhtml .= '</div></div>';
		}
        
        return $resultJson->setData($faqhtml);
    }
}

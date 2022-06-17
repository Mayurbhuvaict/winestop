<?php 
namespace Winestop\Custom\Plugin;
/**
 * 
 */
class Layer
{
	/**
     * @var \Magento\Framework\App\Request\Http
     */
	protected $request;
	/**
     * @param \Magento\Framework\App\Request\Http    
     */
	public function __construct(
		\Magento\Framework\App\Request\Http $request
	) {
		$this->request = $request;
	}

	public function afterGetProductCollection($subject, $collection) {
		$is_show = NULL;        
		$is_show = $this->request->getParam('show-out-of-stock');
		if ($this->request->getFullActionName() == 'catalog_category_view') {
			$collection->addAttributeToFilter('type_id',  array('neq' => 'mpgiftcard'));		
		}
		if ($is_show == '1') {
			
		}
		else
		{
			$collection->getSelect()->where('stock_status_index.stock_status = ?',1);
		}		
		return $collection;
	}
}
<?php
namespace Winestop\Custom\Plugin\Layer\Filter;
/**
 * 
 */
class Category
{
	
	public function afterGetName(\Magento\Catalog\Model\Layer\Filter\Category $catalogConfig, $options)
	{
		return __('Varietal');
	}
}
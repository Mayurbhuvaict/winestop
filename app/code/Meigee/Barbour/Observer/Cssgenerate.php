<?php
namespace Meigee\Barbour\Observer;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
// use \Magento\Store\Model\ScopeInterface;

class Cssgenerate implements ObserverInterface
{
 
	 /**
     * @var RequestInterface
     */
    protected $appRequestInterface;
	private $_cssGenerate;

    public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        RequestInterface $appRequestInterface,
		\Meigee\Barbour\Block\Frontend\CustomDesign $cssGenerate
    ) {
        $this->appRequestInterface = $appRequestInterface;
		$this->_cssGenerate = $cssGenerate;
    }
 
	public function execute(Observer $observer)
	{
		$sectionId = $this->appRequestInterface->getParam('section');
		$is_design = strrpos($sectionId, 'barbour_theme_design');
		
		if($is_design !== false) {
			 $this->_cssGenerate->saveOpt(true, $sectionId);
		}
	}
}


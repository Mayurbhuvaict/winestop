<?php
/**
 * FAQ Index Controller for display FAQ on front-end.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Controller\Index;
 
use Magento\Framework\App\Action\Context;
 
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $resultPageFactory;
    
    /**
     * @var \Rootways\Faq\Helper\Data
    */
	protected $helper;
    
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
    */
    protected $forwardFactory;
    
    /**
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Rootways\Faq\Helper\Data $helper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Rootways\Faq\Helper\Data $helper,
        \Magento\Framework\Controller\Result\ForwardFactory $forwardFactory
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
		$this->_helper = $helper;
        $this->_forwardFactory = $forwardFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        if ($this->_helper->getConfig('rootwaysfaq_option/general/enable')) {
            $resultPage = $this->_resultPageFactory->create();
            $metatitle = $this->_helper->metatitle();
            if ( $metatitle == '' ) {
                $metatitle = 'FAQ';
            }
            $resultPage->getConfig()->getTitle()->set($metatitle);

            $metadescription = $this->_helper->metadescription();
            if ( $metadescription != '' ) {
                $resultPage->getConfig()->setDescription($metadescription);
            }

            $metakeyword = $this->_helper->metakeyword();
            if ( $metakeyword != '' ) {
                $resultPage->getConfig()->setKeywords($metakeyword);
            }
            return $resultPage;
        } else {
            $resultForward = $this->_forwardFactory->create();
            $resultForward->setController('index');
            $resultForward->forward('defaultNoRoute');
            return $resultForward;
        }
    }
}

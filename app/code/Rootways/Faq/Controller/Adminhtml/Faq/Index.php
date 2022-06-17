<?php
/**
 * FAQ Controller for backend-side FAQ.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Controller\Adminhtml\Faq;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Rootways_Faq::faqs');
        $resultPage->addBreadcrumb(
            'Root FAQs Manager',
            'Root FAQs Manager'
        );
        $resultPage->getConfig()->getTitle()->prepend(__('FAQs'));
        $resultPage->getConfig()->getTitle()
            ->prepend('FAQs Manager');
        return $resultPage;
    }
}

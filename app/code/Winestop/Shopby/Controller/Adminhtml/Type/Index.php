<?php
namespace Winestop\Shopby\Controller\Adminhtml\Type;
 
class Index extends \Magento\Backend\App\Action
{

    private $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Winestop_Shopby::type');
        $resultPage->getConfig()->getTitle()->prepend(__('Type List'));
        return $resultPage;
    }

}
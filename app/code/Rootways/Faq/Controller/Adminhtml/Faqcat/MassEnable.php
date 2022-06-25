<?php
/**
 * FAQ Category Controller for backend-side FAQ Category.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Controller\Adminhtml\Faqcat;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Rootways\Faq\Model\ResourceModel\Faqcat\CollectionFactory;

class MassEnable extends \Magento\Backend\App\Action
{
	const ADMIN_RESOURCE = 'Rootways_Faq::category_edit';
	protected $filter;
	protected $collectionFactory;
	
	public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
	
	public function execute()
    {
        
        
        $post = $this->getRequest()->getPostValue();
        if (isset($post['excluded'])) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
        } else {
            $ids = $this->getRequest()->getParams('selected');
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('cat_id', array('in' => $ids));
        }
        
        foreach ($collection as $item) {
           $item->setStatus(1);
            $item->save();
        }
        
        
        
        
        /*
        
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        foreach ($collection as $item) {
            $item->setStatus(1);
            $item->save();
        }
        */

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been activated.', $collection->getSize()));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}

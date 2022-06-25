<?php
/**
 * FAQ model for collect data.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_storeManager;
    const FAQ_QUESTION_PATH = 'faq';
    const FAQ_CATEGORY_PATH = 'faq/category';
    const FAQ_ENTITY_TYPE = 'faq-question';
    const FAQ_DOT_HTML = '.html';
    const FAQ_QUESTION_TARGET_PATH = 'faq/question/view/faq_id/';
    const FAQ_CATEGORY_TARGET_PATH = 'faq/category/view/cat_id/';
    const FAQ_REQUEST_PATH = 'faqs'.Faq::FAQ_DOT_HTML;
    const FAQ_TARGET_PATH = 'faq/faq/view';
	
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }
	
    protected function _construct()
    {
        $this->_init('rootways_faq', 'faq_id');
    }
	
    protected function _afterLoad(AbstractModel $object)
    {
        $faq_store = $this->getConnection()
            ->select()
            ->from($this->getTable('rootways_faq_store'), ['store_id'])
            ->where('faq_id = :faq_id');

        $stores = $this->getConnection()->fetchCol($faq_store, [':faq_id' => $object->getId()]);

        if ($stores) {
            $object->setData('stores', $stores);
        }
        return parent::_afterLoad($object);
    }
	
	protected function _afterSave(AbstractModel $object)
    {
        $this->saveFaqRelation($object);
        return parent::_afterSave($object);
    }
	
    protected function saveFaqRelation(AbstractModel $object)
    {
        $faq_id = $object->getData('faq_id');
        $stores = $object->getData('stores');

        if ($faq_id && (int) $faq_id > 0) {
            $adapter = $this->getConnection();
			
            if ($stores) {
                $condition = ['faq_id = ?' => (int) $faq_id];
                $adapter->delete($this->getTable('rootways_faq_store'), $condition);
				
                $data = [];
                foreach ($stores as $store_id) {
                    //if ($store_id != 0) {
						$data[] = [
                        	'faq_id' => (int) $faq_id,
                        	'store_id' => (int) $store_id
                    	];
					//}
                }
                $adapter->insertMultiple($this->getTable('rootways_faq_store'), $data);
            }
        }
        return $this;
    }
	
    protected function _afterDelete(AbstractModel $object)
    {
        $adapter = $this->getConnection();
        $condition = [
            'entity_type =?' => Faq::FAQ_ENTITY_TYPE,
            'entity_id =?' => (int) $object->getFaqId()
        ];
        $adapter->delete($this->getTable('url_rewrite'), $condition);
        return parent::_afterDelete($object);
    }
}

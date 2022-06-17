<?php
/**
 * FAQ Category model for collect data.
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

class Faqcat extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const FAQ_CATEGORY_ENTITY_TYPE = 'faq-category';
    const FAQ_CATEGORY_FILE_PATH_ACCESS = 'rootways/faq/category/';

    public function __construct(
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('rootways_faq_category', 'cat_id');
    }
    
    protected function _afterLoad(AbstractModel $object)
    {
        $faq_store = $this->getConnection()
            ->select()
            ->from($this->getTable('rootways_faq_cat_store'), ['store_id'])
            ->where('cat_id = :cat_id');

        $stores = $this->getConnection()->fetchCol($faq_store, [':cat_id' => $object->getId()]);

        if ($stores) {
            $object->setData('stores', $stores);
        }
        return parent::_afterLoad($object);
    }
	
	protected function _afterSave(AbstractModel $object)
    {
        $this->saveFaqCatRelation($object);
        return parent::_afterSave($object);
    }
	
    protected function saveFaqCatRelation(AbstractModel $object)
    {
        $cat_id = $object->getData('cat_id');
        $stores = $object->getData('stores');

        if ($cat_id && (int) $cat_id > 0) {
            $adapter = $this->getConnection();
			
            if ($stores) {
                $condition = ['cat_id = ?' => (int) $cat_id];
                $adapter->delete($this->getTable('rootways_faq_cat_store'), $condition);
				
                $data = [];
                foreach ($stores as $store_id) {
                    //if ($store_id != 0) {
						$data[] = [
                        	'cat_id' => (int) $cat_id,
                        	'store_id' => (int) $store_id
                    	];
					//}
                }
                $adapter->insertMultiple($this->getTable('rootways_faq_cat_store'), $data);
            }
        }
        return $this;
    }
}

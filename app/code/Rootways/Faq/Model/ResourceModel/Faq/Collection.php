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

namespace Rootways\Faq\Model\ResourceModel\Faq;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'faq_id';
    protected function _construct()
    {
        $this->_init('Rootways\Faq\Model\Faq', 'Rootways\Faq\Model\ResourceModel\Faq');
    }
}

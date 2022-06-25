<?php
/**
 * FAQ Category Model for collect data.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Model;

class Faqcat extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'rootways_faq_category';
    protected function _construct()
    {
        $this->_init('Rootways\Faq\Model\ResourceModel\Faqcat');
    }
}

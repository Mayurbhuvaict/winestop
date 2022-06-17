<?php
/**
 * Block for get information of FAQ.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Block\Adminhtml;

class Faq extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Rootways_Faq';
        $this->_controller = 'Adminhtml_Faq';
        $this->_headerText = __('Rootways New FAQs Manager File From Block');
        $this->_addButtonLabel = __('Add New FAQ File From Block');
        parent::_construct();
    }
}

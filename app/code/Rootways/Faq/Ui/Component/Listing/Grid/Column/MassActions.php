<?php
/**
 * FAQ category UI file for collect data.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class MassActions extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected function _initSelect()  {
        $this->addFilterToMap('faq_id', 'main_table.faq_id');
        parent::_initSelect();
        
        $this->getSelect()->joinLeft(
            ['faq_store' => $this->getTable('rootways_faq_store')],
            'main_table.faq_id = faq_store.faq_id',
            ['store_id']
        );
        return $this;
        /*
        $this->getSelect()->joinLeft(
            ['faq_store' => $this->getTable('rootways_faq_store')],
            'main_table.faq_id = faq_store.faq_id',
            ['store_id']
        );
        $this->getSelect()->group(
            'main_table.faq_id'
        );
        parent::_renderFiltersBefore();
        */
     }
}

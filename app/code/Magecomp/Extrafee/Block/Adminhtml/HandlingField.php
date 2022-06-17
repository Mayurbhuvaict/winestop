<?php
namespace Magecomp\Extrafee\Block\Adminhtml;
 
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
 
/**
 * Class HandlingField
 */
class HandlingField extends AbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('box', ['label' => __('Box'), 'class' => '']);
        $this->addColumn('fee', ['label' => __('Fee'), 'class' => '']);
        $this->addColumn('weight', ['label' => __('Weight'), 'class' => '']);
        /*$this->addColumn('weight', ['label' => __('Weight'), 'class' => '']);*/
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add handling');
    }
}
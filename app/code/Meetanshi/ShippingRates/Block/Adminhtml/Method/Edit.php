<?php

namespace Meetanshi\ShippingRates\Block\Adminhtml\Method;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

class Edit extends Container
{
    protected $coreRegistry = null;

    public function __construct(Context $context, Registry $registry, array $data = [])
    {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'Meetanshi_ShippingRates';
        $this->_controller = 'adminhtml_method';

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            -100
        );
    }

    public function getHeaderText()
    {
        $header = __('New Method');
        $model = $this->coreRegistry->registry('shippingrates_method');
        if ($model->getId()) {
            $header = __('Edit Method `%1`', $model->getName());
        }
        return $header;
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('shippingrates/method/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
}

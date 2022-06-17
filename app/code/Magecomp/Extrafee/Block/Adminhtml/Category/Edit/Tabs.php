<?php

namespace Magecomp\Extrafee\Block\Adminhtml\Category\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

class Tabs extends WidgetTabs
{
    protected $registry;

    public function __construct(Registry $registry, Context $context, EncoderInterface $jsonEncoder, Session $authSession)
    {
        $this->registry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('categoryTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', [
            'label' => __('General'),
            'title' => __('General'),
            'content' => $this->getLayout()->createBlock('\Magecomp\Extrafee\Block\Adminhtml\Category\Edit\Tab\General')->toHtml(),
        ]);

        $this->_updateActiveTab();

        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        } else {
            $this->setActiveTab('main');
        }
    }
}

<?php
namespace Winestop\Testimonials\Block\Adminhtml\Testimonials\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('testimonials_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Testimonials Information'));
    }
}
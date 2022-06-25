<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Block\Adminhtml\Template\Edit;

use Exception;
use Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab\Design;
use Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab\Images;
use Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab\Information;

/**
 * Class Tabs
 * @package Mageplaza\GiftCard\Block\Adminhtml\Template\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('giftcard_template_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Template Information'));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('information', Information::class);
        $this->addTab('design', Design::class);
        $this->addTab('images', Images::class);
    }
}

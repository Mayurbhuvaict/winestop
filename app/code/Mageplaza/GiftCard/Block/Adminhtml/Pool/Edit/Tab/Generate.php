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

namespace Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Generate
 * @package Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab
 */
class Generate extends Template implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_GiftCard::pool/generate.phtml';

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Generate Gift Cards');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Generate Gift Cards');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}

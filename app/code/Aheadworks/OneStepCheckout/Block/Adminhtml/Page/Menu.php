<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Page;

use Magento\Backend\Block\Template;

/**
 * Page Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Page
 * @codeCoverageIgnore
 */
class Menu extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_OneStepCheckout::page/menu.phtml';
}

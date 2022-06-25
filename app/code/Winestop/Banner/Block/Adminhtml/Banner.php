<?php
/**
 * Class Banner
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop <magento@Winestop-technologies.com>
 * @license  https://www.Winestop-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.Winestop-technologies.com
 */
namespace Winestop\Banner\Block\Adminhtml;

/**
 * Class Banner
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop <magento@Winestop-technologies.com>
 * @license  https://www.Winestop-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.Winestop-technologies.com
 */
class Banner extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Winestop_Banner';
        $this->_controller = 'adminhtml';
        $this->_headerText = __('Banner');
        $this->_addButtonLabel = __('Add New Banner');
        parent::_construct();
    }
}

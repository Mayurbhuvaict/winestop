<?php
/**
 * Class Form
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop <magento@Winestop-technologies.com>
 * @license  https://www.Winestop-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.Winestop-technologies.com
 */
namespace Winestop\Banner\Block\Adminhtml\Banner\Edit;

/**
 * Class Form
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop <magento@Winestop-technologies.com>
 * @license  https://www.Winestop-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.Winestop-technologies.com
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /**
         * FormFactory
         *
         * @var \Magento\Framework\Data\Form $form
         */
        $form = $this->_formFactory->create(
            [
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

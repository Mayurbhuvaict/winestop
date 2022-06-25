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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Controller\Adminhtml\Template;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class Edit
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Template
 */
class Edit extends Template
{
    /** @var Registry */
    protected $registry;

    /** @var Data */
    protected $_giftcardHelper;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     * @param Registry $registry
     * @param Data $giftcardHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        Registry $registry,
        Data $giftcardHelper
    ) {
        $this->registry        = $registry;
        $this->_giftcardHelper = $giftcardHelper;

        parent::__construct($context, $resultPageFactory, $templateFactory);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->_initAction();

        $template = $this->_initObject();

        if (!$template) {
            $resultPage->getConfig()->getTitle()->prepend(__('Create New Template'));

            return $resultPage;
        }

        $templateCollection = $this->_getTemplateCollection();
        $template->setTemplateCollection($templateCollection->getData());

        //Set entered data if was error when we do save
        $data = $this->_session->getTemplateFormData(true);
        if (!empty($data)) {
            $template->addData($data);
        }

        $this->registry->register('current_template', $template);

        /** @var Page $resultPage */
        $resultPage = $this->_initAction();

        $resultPage->getConfig()->getTitle()->prepend($template->getId() ? __(
            'Edit Template "%1"',
            $template->getName()
        ) : __('Create New Template'));

        return $resultPage;
    }
}

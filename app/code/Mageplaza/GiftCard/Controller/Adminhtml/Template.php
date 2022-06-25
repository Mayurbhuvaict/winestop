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

namespace Mageplaza\GiftCard\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class Template
 * @package Mageplaza\GiftCard\Controller\Adminhtml
 */
abstract class Template extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::template';

    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var TemplateFactory */
    protected $_templateFactory;

    /**
     * Template constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_templateFactory  = $templateFactory;

        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_GiftCard::template');
        $resultPage->addBreadcrumb(__('Gift Card'), __('Gift Card'));
        $resultPage->addBreadcrumb(__('Templates'), __('Templates'));

        return $resultPage;
    }

    /**
     * Init Gift Code
     *
     * @return bool|\Mageplaza\GiftCard\Model\Template
     */
    protected function _initObject()
    {
        $id = (int) $this->getRequest()->getParam('id');

        /** @var \Mageplaza\GiftCard\Model\Template $template */
        $template = $this->_templateFactory->create();
        if ($id) {
            $template->load($id);
            if (!$template->getId()) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));

                return false;
            }
        }

        return $template;
    }

    /**
     * Get gift code collection
     *
     * @return mixed
     */
    protected function _getTemplateCollection()
    {
        return $this->_templateFactory->create()->getCollection();
    }
}

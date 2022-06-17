<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\SeoContent\Service\ContentService;

abstract class Template extends Action
{
    /**
     * @var TemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;
    /**
     * @var ContentService
     */
    protected $contentService;

    /**
     * Template constructor.
     * @param TemplateRepositoryInterface $templateRepository
     * @param Registry $registry
     * @param Context $context
     * @param ContentService $contentService
     */
    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        Registry $registry,
        Context $context,
        ContentService $contentService
    ) {
        $this->templateRepository = $templateRepository;
        $this->registry           = $registry;
        $this->context            = $context;
        $this->contentService     = $contentService;
        $this->resultFactory      = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return TemplateInterface
     */
    protected function initModel()
    {
        $template = $this->templateRepository->create();

        if ($this->getRequest()->getParam(TemplateInterface::ID)) {
            $template = $this->templateRepository->get($this->getRequest()->getParam(TemplateInterface::ID));
        }

        $this->registry->register(TemplateInterface::class, $template);

        return $template;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_SeoContent::seo_content_template');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced SEO Suite'));
        $resultPage->getConfig()->getTitle()->prepend(__('Template Manager'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_SeoContent::seo_content_template');
    }
}

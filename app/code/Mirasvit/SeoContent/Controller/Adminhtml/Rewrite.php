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
use Mirasvit\SeoContent\Api\Data\RewriteInterface;
use Mirasvit\SeoContent\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoContent\Service\ContentService;

abstract class Rewrite extends Action
{
    /**
     * @var RewriteRepositoryInterface
     */
    protected $rewriteRepository;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;
    /**
     * @var ContentService
     */
    protected $contentService;

    /**
     * Rewrite constructor.
     * @param RewriteRepositoryInterface $rewriteRepository
     * @param ContentService $contentService
     * @param Context $context
     */
    public function __construct(
        RewriteRepositoryInterface $rewriteRepository,
        ContentService $contentService,
        Context $context
    ) {
        $this->rewriteRepository = $rewriteRepository;
        $this->contentService = $contentService;

        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return RewriteInterface
     */
    protected function initModel()
    {
        $rewrite = $this->rewriteRepository->create();

        if ($this->getRequest()->getParam(RewriteInterface::ID)) {
            $rewrite = $this->rewriteRepository->get($this->getRequest()->getParam(RewriteInterface::ID));
        }

        return $rewrite;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_SeoContent::seo_content_rewrite');
        $resultPage->getConfig()->getTitle()->prepend(__('Advanced SEO Suite'));
        $resultPage->getConfig()->getTitle()->prepend(__('Rewrite Manager'));

        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_SeoContent::seo_content_rewrite');
    }
}

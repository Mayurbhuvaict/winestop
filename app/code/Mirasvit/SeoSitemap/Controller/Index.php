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



namespace Mirasvit\SeoSitemap\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\SeoSitemap\Service\SeoSitemapUrlService;

abstract class Index extends Action
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;
    /**
     * @var SeoSitemapUrlService
     */
    protected $seoSitemapUrlService;
    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param SeoSitemapUrlService $seoSitemapUrlService
     */
    public function __construct(
        Context $context,
        SeoSitemapUrlService $seoSitemapUrlService
    ) {
        $this->context              = $context;
        $this->request              = $context->getRequest();
        $this->response             = $context->getResponse();
        $this->resultFactory        = $context->getResultFactory();
        $this->seoSitemapUrlService = $seoSitemapUrlService;
        parent::__construct($context);
    }
}

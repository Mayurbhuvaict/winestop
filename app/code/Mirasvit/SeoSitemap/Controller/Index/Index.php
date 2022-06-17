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



namespace Mirasvit\SeoSitemap\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Mirasvit\SeoSitemap\Controller\Index
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->correctUrlRedirect();
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $resultPage;
    }

    /**
     * @return void
     */
    public function correctUrlRedirect()
    {
        $originalPathInfo = $this->request->getOriginalPathInfo();
        $fullActionName = $this->request->getFullActionName();

        if (('seositemap_index_index' == $fullActionName)
            && !preg_match('/' . preg_quote($this->seoSitemapUrlService->getConfigUrSuffix(), '/') .'$/',
                $originalPathInfo
            )) {
            $url = $this->seoSitemapUrlService->getBaseUrl();
            $this->response->setRedirect($url, 301)->sendResponse();
        }
    }
}
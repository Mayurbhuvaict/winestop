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



namespace Mirasvit\Seo\Controller\Adminhtml\Duplicateinfo;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Mirasvit\Seo\Controller\Adminhtml\Duplicateinfo
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
         $seoSectionUrl = $this->_url->getUrl(
                            'adminhtml/system_config/edit',
                            ['section' => 'seo']
                        );

        $this->messageManager->addNotice( __('Create a unique url key for all of your categories listed on this table.'
            . ' If the table is empty, this means that you do not have duplicate keys and can push the "<a href="'
            . $seoSectionUrl . '" target="_blank">Remove Parent Category Path</a>" button to change category urls.') );

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->getConfig()->getTitle()->prepend(__('Category duplicate urls'));
        $this->_initAction();
        $this->_addContent($resultPage->getLayout()
            ->createBlock('\Mirasvit\Seo\Block\Adminhtml\Duplicateinfo'));

        return $resultPage;
    }
}

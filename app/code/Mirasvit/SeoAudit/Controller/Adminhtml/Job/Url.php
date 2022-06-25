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


declare(strict_types=1);

namespace Mirasvit\SeoAudit\Controller\Adminhtml\Job;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Controller\Adminhtml\Job;
use Mirasvit\SeoAudit\Model\ConfigProvider;
use Mirasvit\SeoAudit\Model\Config\Source\Check;
use Mirasvit\SeoAudit\Repository\JobRepository;

class Url extends Job
{
    private $checkSource;

    public function __construct(
        Check          $checkSource,
        ConfigProvider $config,
        JobRepository  $jobRepository,
        Registry       $registry,
        Context        $context
    ) {
        $this->checkSource = $checkSource;

        parent::__construct($config, $jobRepository, $registry, $context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id         = $this->getRequest()->getParam(CheckResultInterface::JOB_ID);
        $identifier = $this->getRequest()->getParam(CheckResultInterface::IDENTIFIER);
        $resultType = $this->getRequest()->getParam(CheckResultInterface::RESULT);

        $model = $this->initModel();

        if ($id && !$model) {
            $this->messageManager->addErrorMessage((string)__('This job no longer exists.'));

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $title = '';

        if ($identifier) {
            $title .= $this->checkSource->getLabel($identifier);
        }

        if ($resultType) {
            $title = '[' . ucfirst($resultType) . '] ' . $title;
        }

        $this->addDisabledWarningMessage();

        $resultPage->getConfig()->getTitle()->prepend($title);

        $this->_initAction();

        return $resultPage;
    }
}

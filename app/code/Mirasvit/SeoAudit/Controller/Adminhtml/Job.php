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


namespace Mirasvit\SeoAudit\Controller\Adminhtml;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Model\ConfigProvider;
use Mirasvit\SeoAudit\Repository\JobRepository;

abstract class Job extends Action
{
    protected $config;

    protected $context;

    protected $jobRepository;

    protected $registry;

    public function __construct(
        ConfigProvider $config,
        JobRepository $jobRepository,
        Registry $registry,
        Context $context
    ) {
        $this->config        = $config;
        $this->jobRepository = $jobRepository;
        $this->context       = $context;
        $this->registry      = $registry;

        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->context->getAuthorization()
            ->isAllowed('Mirasvit_SeoAudit::seoaudit_job');
    }

    protected function _initAction(): Action
    {
        $this->_setActiveMenu('seo');

        return $this;
    }

    protected function initModel(): ?JobInterface
    {
        $job = $this->jobRepository->create();

        if ($id = $this->getRequest()->getParam(JobInterface::ID)) {
            $job = $this->jobRepository->get((int)$id);
        }

        $this->registry->register(JobInterface::class, $job);

        return $job;
    }

    protected function addDisabledWarningMessage(): void
    {
        if (!$this->config->isEnabled()) {
            $this->getMessageManager()->addWarningMessage(
                (string)__('SEO Audit disabled in configurations')
            );
        }
    }
}

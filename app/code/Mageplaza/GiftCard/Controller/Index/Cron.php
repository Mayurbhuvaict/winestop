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

namespace Mageplaza\GiftCard\Controller\Index;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Cron\Notification;
use Mageplaza\GiftCard\Cron\Process;

/**
 * Class Check
 * @package Magento\Customer\Controller\Ajax
 */
class Cron extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var Notification
     */
    protected $notification;

    /**
     * @var Process
     */
    protected $process;

    /**
     * Cron constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Notification $cronNotification
     * @param Process $cronProcess
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Notification $cronNotification,
        Process $cronProcess
    ) {
        $this->pageFactory  = $resultPageFactory;
        $this->notification = $cronNotification;
        $this->process      = $cronProcess;

        parent::__construct($context);
    }

    /**
     * test cron
     * @throws Exception
     */
    public function execute()
    {
        $this->notification->execute();
        $this->process->execute();
    }
}

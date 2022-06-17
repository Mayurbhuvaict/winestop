<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */


















namespace Aheadworks\AdvancedReports\Controller\Adminhtml\Statistics;

use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as StatisticsProcessor;

/**
 * Class Schedule
 *
 * @package Aheadworks\AdvancedReports\Controller\Adminhtml\Statistics
 */
class Schedule extends \Magento\Backend\App\Action
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReports::reports_statistics';

    /**
     * @var StatisticsProcessor
     */
    private $statisticsProcessor;

    /**
     * @param Context $context
     * @param StatisticsProcessor $statisticsProcessor
     */
    public function __construct(
        Context $context,
        StatisticsProcessor $statisticsProcessor
    ) {
        parent::__construct($context);
        $this->statisticsProcessor = $statisticsProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->statisticsProcessor->markIndexerAsInvalid();
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }
}

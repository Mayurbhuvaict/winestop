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


















































namespace Aheadworks\AdvancedReports\Model\Email\Report;

use Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyCheckerPool;

/**
 * Class Scheduler
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report
 */
class Scheduler
{
    /**
     * @var FrequencyCheckerPool
     */
    private $checkerPool;

    /**
     * @param FrequencyCheckerPool $checkerPool
     */
    public function __construct(
        FrequencyCheckerPool $checkerPool
    ) {
        $this->checkerPool = $checkerPool;
    }

    /**
     * Check if email can be sent
     *
     * @param ConfigInterface $config
     * @return bool
     */
    public function isScheduledToBeSent(ConfigInterface $config)
    {
        $frequencyChecker = $this->checkerPool->getChecker($config->getWhenToSendFrequency());
        return $frequencyChecker->isMatched();
    }
}

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



































































namespace Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyChecker;

use Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyCheckerInterface;

/**
 * Class LastMonth
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyChecker
 */
class LastMonth extends AbstractChecker implements FrequencyCheckerInterface
{
    /**
     * @inheritdoc
     */
    public function isMatched()
    {
        $now = $this->getCurrentDate();
        return (int)$now->format('d') == 1;
    }
}

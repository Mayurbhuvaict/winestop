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
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class AbstractChecker
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyChecker
 */
abstract class AbstractChecker implements FrequencyCheckerInterface
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        TimezoneInterface $localeDate
    ) {
        $this->localeDate = $localeDate;
    }

    /**
     * @inheritdoc
     */
    abstract public function isMatched();

    /**
     * Retrieve locale timezone
     *
     * @return string
     */
    protected function getLocaleTimezone()
    {
        return $this->localeDate->getConfigTimezone(ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * Get current date
     *
     * @return \DateTime
     */
    protected function getCurrentDate()
    {
        $timezone = new \DateTimeZone($this->getLocaleTimezone());
        return new \DateTime('now', $timezone);
    }
}

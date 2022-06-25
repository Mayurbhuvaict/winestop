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































































namespace Aheadworks\AdvancedReports\Model\Email\Report\Scheduler;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReports\Model\Source\Email\Frequency;
use Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyChecker\LastWeek;
use Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyChecker\LastMonth;
use Aheadworks\AdvancedReports\Model\Email\Report\Scheduler\FrequencyChecker\LastQuarter;

/**
 * Class FrequencyCheckerPool
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Scheduler
 */
class FrequencyCheckerPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $checkers = [
        Frequency::TYPE_LAST_WEEK => LastWeek::class,
        Frequency::TYPE_LAST_MONTH => LastMonth::class,
        Frequency::TYPE_LAST_QUARTER => LastQuarter::class
    ];

    /**
     * @var FrequencyCheckerInterface[]
     */
    private $checkerInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $checkers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $checkers = []
    ) {
        $this->objectManager = $objectManager;
        $this->checkers = array_merge($this->checkers, $checkers);
    }

    /**
     * Retrieve checker by frequency type
     *
     * @param string $frequency
     * @return FrequencyCheckerInterface
     * @throws \InvalidArgumentException
     */
    public function getChecker($frequency)
    {
        if (!isset($this->checkerInstances[$frequency])) {
            if (!isset($this->checkers[$frequency])) {
                throw new \InvalidArgumentException($frequency . ' is unknown checker');
            }

            $checkerInstance = $this->objectManager->create($this->checkers[$frequency]);
            if (!$checkerInstance instanceof FrequencyCheckerInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Checker instance %s does not implement required interface.', $frequency)
                );
            }
            $this->checkerInstances[$frequency] = $checkerInstance;
        }
        return $this->checkerInstances[$frequency];
    }
}

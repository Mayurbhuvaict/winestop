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








































































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var []
     */
    private $resourceModelNames = [
        Day::KEY => Day::class,
        Week::KEY => Week::class,
        Month::KEY => Month::class,
        Quarter::KEY => Quarter::class,
        Year::KEY => Year::class
    ];

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create date grouping object
     *
     * @param string $key
     * @param array $data
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\AbstractResource
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($key, array $data = [])
    {
        if (!isset($this->resourceModelNames[$key])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Key %1 doesn\'t exist in map', $key));
        }
        return $this->objectManager->create($this->resourceModelNames[$key], $data);
    }
}

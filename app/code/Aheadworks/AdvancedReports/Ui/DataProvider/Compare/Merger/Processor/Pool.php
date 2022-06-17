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





























































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Pool
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Processor
 */
class Pool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var MergerInterface[]
     */
    private $mergers;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $mergers
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $mergers = []
    ) {
        $this->objectManager = $objectManager;
        $this->mergers = $mergers;
    }

    /**
     * Retrieve merger by name
     *
     * @param string $mergerName
     * @param array|null $config
     * @return MergerInterface
     * @throws NotFoundException
     */
    public function getMerger($mergerName, $config = [])
    {
        if (!isset($this->mergers[$mergerName])) {
            throw new NotFoundException(__('Unknown merger: %s requested', $mergerName));
        }
        $arguments = !empty($config) ? ['data' => $config] : [];
        $merger = $this->objectManager->create($this->mergers[$mergerName], $arguments);

        return $merger;
    }
}

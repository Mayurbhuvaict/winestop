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















































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

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
     * @param string $className
     * @param array $data
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\AbstractResource
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className, array $data = [])
    {
        $model = $this->objectManager->create($className, $data);
        if (!$model instanceof \Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\AbstractResource) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    '%1 doesn\'t extends '
                    . '\Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\AbstractResource',
                    $className
                )
            );
        }
        return $model;
    }
}

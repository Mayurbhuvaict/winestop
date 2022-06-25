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





































































































































































































































namespace Aheadworks\AdvancedReports\Ui\DataProvider;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class MetadataPool
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class MetadataPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $metadata = [];

    /**
     * @var MetadataInterface[]
     */
    private $metadataInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $metadata
     */
    public function __construct(ObjectManagerInterface $objectManager, $metadata = [])
    {
        $this->objectManager = $objectManager;
        $this->metadata = $metadata;
    }

    /**
     * Retrieves metadata for engine code
     *
     * @param string $dataSourceName
     * @return MetadataInterface
     * @throws NotFoundException
     */
    public function getMetadata($dataSourceName)
    {
        if (!isset($this->metadataInstances[$dataSourceName])) {
            if (!isset($this->metadata[$dataSourceName])) {
                throw new NotFoundException(__('Unknown data provider metadata: %s requested', $dataSourceName));
            }
            $this->metadataInstances[$dataSourceName] = $this->objectManager->create(
                MetadataInterface::class,
                ['data' => $this->metadata[$dataSourceName]]
            );
        }
        return $this->metadataInstances[$dataSourceName];
    }
}

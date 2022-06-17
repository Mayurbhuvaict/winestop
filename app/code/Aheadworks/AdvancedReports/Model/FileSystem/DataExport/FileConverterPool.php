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












































































































































































































namespace Aheadworks\AdvancedReports\Model\FileSystem\DataExport;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReports\Model\Source\Email\Format;
use Aheadworks\AdvancedReports\Model\FileSystem\DataExport\ConvertTo\Csv as ConvertToCsv;
use Aheadworks\AdvancedReports\Model\FileSystem\DataExport\ConvertTo\Xml as ConvertToXml;

/**
 * Class FileConverterPool
 *
 * @package Aheadworks\AdvancedReports\Model\FileSystem\DataExport
 */
class FileConverterPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $fileConverters = [
        Format::TYPE_CSV => ConvertToCsv::class,
        Format::TYPE_EXCEL_XML => ConvertToXml::class,
    ];

    /**
     * @var ConverterInterface[]
     */
    private $fileConverterInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $fileConverters
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $fileConverters = []
    ) {
        $this->objectManager = $objectManager;
        $this->fileConverters = array_merge($this->fileConverters, $fileConverters);
    }

    /**
     * Retrieve file converter for file format
     *
     * @param string $fileFormat
     * @return ConverterInterface
     * @throws \InvalidArgumentException
     */
    public function getFileConverter($fileFormat)
    {
        if (!isset($this->fileConverterInstances[$fileFormat])) {
            if (!isset($this->fileConverters[$fileFormat])) {
                throw new \InvalidArgumentException(
                    sprintf('File format %s is not supported.', $fileFormat)
                );
            }

            $formatterInstance = $this->objectManager->create($this->fileConverters[$fileFormat]);
            if (!$formatterInstance instanceof ConverterInterface) {
                throw new \InvalidArgumentException(
                    sprintf('File converter instance %s does not implement required interface.', $fileFormat)
                );
            }
            $this->fileConverterInstances[$fileFormat] = $formatterInstance;
        }
        return $this->fileConverterInstances[$fileFormat];
    }
}

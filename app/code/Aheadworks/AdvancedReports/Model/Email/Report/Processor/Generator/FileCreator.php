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























































namespace Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator;

use Aheadworks\AdvancedReports\Model\FileSystem\FileFactory;
use Aheadworks\AdvancedReports\Model\FileSystem\DataExport\FileConverterPool;

/**
 * Class FileCreator
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Processor\Generator
 */
class FileCreator
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var FileConverterPool
     */
    private $fileConverterPool;

    /**
     * @param FileFactory $fileFactory
     * @param FileConverterPool $fileConverterPool
     */
    public function __construct(
        FileFactory $fileFactory,
        FileConverterPool $fileConverterPool
    ) {
        $this->fileFactory = $fileFactory;
        $this->fileConverterPool = $fileConverterPool;
    }

    /**
     * Get file content
     *
     * @param string $fileFormat
     * @return string
     * @throws \Exception
     */
    public function create($fileFormat)
    {
        $fileConverter = $this->fileConverterPool->getFileConverter($fileFormat);
        return $this->fileFactory->create($fileConverter->getFile(), 'var');
    }
}

<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Cron;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Driver\File;
use Mageplaza\GiftCard\Helper\Template;

/**
 * Class RemoveTmpImages
 * @package Mageplaza\GiftCard\Cron
 */
class RemoveTmpImages
{
    /**
     * @var Template
     */
    private $_helper;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var File
     */
    private $driverFile;

    /**
     * RemoveTmpImages constructor.
     *
     * @param Template $templateHelper
     * @param File $driverFile
     */
    public function __construct(
        Template $templateHelper,
        File $driverFile
    ) {
        $this->_helper        = $templateHelper;
        $this->mediaDirectory = $templateHelper->getMediaDirectory();
        $this->driverFile     = $driverFile;
    }

    /**
     * @throws FileSystemException
     */
    public function execute()
    {
        $this->readDir($this->_helper->getBaseTmpMediaPath());
    }

    /**
     * Read and remove images which create from before 3 days ago
     *
     * @param $path
     *
     * @throws FileSystemException
     */
    public function readDir($path)
    {
        $items = $this->mediaDirectory->read($path);
        foreach ($items as $item) {
            if ($this->mediaDirectory->isDirectory($item)) {
                $this->readDir($item);
            } else {
                $file     = $this->mediaDirectory->getAbsolutePath($item);
                $fileData = $this->driverFile->stat($file);
                if (isset($fileData['mtime']) && $fileData['mtime'] < strtotime('-3days')) {
                    $this->mediaDirectory->delete($item);
                }
            }
        }
        if (!count($items)) {
            $this->mediaDirectory->delete($path);
        }
    }
}

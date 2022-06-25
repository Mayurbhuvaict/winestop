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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Pool;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Mageplaza\GiftCard\Helper\Media;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Import;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class ImportCsv
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class ImportCsv extends ManualAdd
{
    /**
     * @var Csv
     */
    private $csvProcessor;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Import
     */
    private $import;

    /**
     * @var Media
     */
    private $helperFile;

    /**
     * ImportCsv constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param CollectionFactory $collectionFactory
     * @param GiftCardFactory $cardFactory
     * @param LoggerInterface $logger
     * @param Csv $csvProcessor
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param File $file
     * @param Import $import
     * @param Media $helperFile
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        CollectionFactory $collectionFactory,
        GiftCardFactory $cardFactory,
        LoggerInterface $logger,
        Csv $csvProcessor,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        File $file,
        Import $import,
        Media $helperFile
    ) {
        $this->csvProcessor    = $csvProcessor;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem      = $filesystem;
        $this->file            = $file;
        $this->import          = $import;
        $this->helperFile      = $helperFile;

        parent::__construct($context, $resultPageFactory, $poolFactory, $collectionFactory, $cardFactory, $logger);
    }

    /**
     * @param Pool $pool
     *
     * @return array
     * @throws LocalizedException
     * @throws Exception
     */
    public function generate($pool)
    {
        $fileInfo = $this->uploadFile();

        if (empty($fileInfo)) {
            return [];
        }

        $rawData = $this->csvProcessor->setDelimiter(',')->getData($fileInfo['path'] . $fileInfo['file']);

        /** validate & change raw data to usage array bunch */
        $bunchData = $this->import->processDataBunch($rawData);

        /** remove file directory after validated data */
        if ($this->file->isExists($fileInfo['path'] . $fileInfo['file'])) {
            $this->file->deleteDirectory($fileInfo['path']);
        }

        if (empty($bunchData)) {
            return [];
        }

        $codes = array_unique(array_column($bunchData, 'code'));

        return $this->generateByCodes($pool, $codes);
    }

    /**
     * @return array
     * @throws ValidatorException
     */
    private function uploadFile()
    {
        /** Upload file to media directory */
        $uploader = $this->uploaderFactory->create(['fileId' => 'import_file']);
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        /** @var Read $mediaDirectory */
        $mediaDirectory   = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileAbsolutePath = $mediaDirectory->getAbsolutePath($this->helperFile->getBaseMediaPath('csv'));

        try {
            return $uploader->save($fileAbsolutePath);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return [];
    }
}

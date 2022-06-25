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














































































































































































































namespace Aheadworks\AdvancedReports\Model\FileSystem\DataExport\ConvertTo;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Convert\Excel;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\SearchResultIteratorFactory;
use Magento\Ui\Model\Export\SearchResultIterator;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider;
use Aheadworks\AdvancedReports\Model\FileSystem\DataExport\ConverterInterface;

/**
 * Class Xml
 *
 * @package Aheadworks\AdvancedReports\Model\FileSystem\DataExport\ConvertTo
 */
class Xml implements ConverterInterface
{
    /**
     * @var DirectoryList
     */
    private $directory;

    /**
     * @var ExcelFactory
     */
    private $excelFactory;

    /**
     * @var MetadataProvider
     */
    private $metadataProvider;

    /**
     * @var SearchResultIteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param ExcelFactory $excelFactory
     * @param MetadataProvider $metadataProvider
     * @param SearchResultIteratorFactory $iteratorFactory
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        ExcelFactory $excelFactory,
        MetadataProvider $metadataProvider,
        SearchResultIteratorFactory $iteratorFactory
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->excelFactory = $excelFactory;
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * Returns XML file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getFile()
    {
        $component = $this->filter->getComponent();
        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $dataProvider->setLimit(0, 0);

        /** @var SearchResultInterface $searchResult */
        $searchResult = $component->getContext()->getDataProvider()->getSearchResult();
        /** @var DocumentInterface[] $searchResultItems */
        $searchResultItems = $searchResult->getItems();
        $fields = $this->metadataProvider->getFields($component);

        $resultRows = [];
        /** @var  $item */
        foreach ($searchResultItems as $item) {
            $resultRows[] = $this->metadataProvider->getRowData($component, $item, $fields);
        }
        $totalItemsCount = (int)$searchResult->getTotalCount();
        if ($totalItemsCount > 0) {
            $data = $dataProvider->getData();
            if (isset($data['totals']) && is_array($data['totals'])) {
                foreach ($data['totals'] as $totalsItem) {
                    $resultRows[] = $this->metadataProvider->getTotalRowData($component, $totalsItem, $fields);
                }
            }
        }

        /** @var SearchResultIterator $searchResultIterator */
        $searchResultIterator = $this->iteratorFactory->create(['items' => $resultRows]);

        /** @var Excel $excel */
        $excel = $this->excelFactory->create(
            [
                'iterator' => $searchResultIterator,
                'rowCallback'=> function ($row) {
                    return $row;
                },
            ]
        );

        $name = sha1(microtime());
        $file = 'export/'. $component->getName() . $name . '.xml';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $excel->setDataHeader($this->metadataProvider->getHeaders($component));
        $excel->write($stream, $component->getName() . '.xml');

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true
        ];
    }
}

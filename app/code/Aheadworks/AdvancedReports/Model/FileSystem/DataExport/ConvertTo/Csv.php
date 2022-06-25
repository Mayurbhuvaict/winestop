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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider;
use Aheadworks\AdvancedReports\Model\FileSystem\DataExport\ConverterInterface;

/**
 * Class Csv
 *
 * @package Aheadworks\AdvancedReports\Model\FileSystem\DataExport\ConvertTo
 */
class Csv implements ConverterInterface
{
    /**
     * @var DirectoryList
     */
    private $directory;

    /**
     * @var MetadataProvider
     */
    private $metadataProvider;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var int|null
     */
    private $pageSize = null;

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        $pageSize = 200
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->pageSize = $pageSize;
    }

    /**
     * Prepare CSV file
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
        $fields = $this->metadataProvider->getFields($component);

        $this->directory->create('export');
        $name = sha1(microtime());
        $file = 'export/'. $component->getName() . $name . '.csv';

        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));

        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);

        $totalItemsCount = (int)$dataProvider->getSearchResult()->getTotalCount();
        $totalCount = $totalItemsCount;
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            foreach ($items as $item) {
                $stream->writeCsv($this->metadataProvider->getRowData($component, $item, $fields));
            }

            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }

        if ($totalItemsCount > 0) {
            $data = $dataProvider->getData();
            if (isset($data['totals']) && is_array($data['totals'])) {
                foreach ($data['totals'] as $totalsItem) {
                    $stream->writeCsv($this->metadataProvider->getTotalRowData($component, $totalsItem, $fields));
                }
            }
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true
        ];
    }
}

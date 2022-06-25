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
























































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Manager as CompareMergerManager;
use Magento\Ui\Component\Container;

/**
 * Class DataMerger
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing
 */
class DataMerger extends Container
{
    /**
     * @var CompareMergerManager
     */
    private $mergerManager;

    /**
     * @param ContextInterface $context
     * @param CompareMergerManager $mergerManager
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        CompareMergerManager $mergerManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->mergerManager = $mergerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if ($dataSource['data']['compareEnabled']) {
            $dsName = $this->context->getDataProvider()->getName();
            $data = $dataSource['data'];

            //$dataSource['data']['items'] = $this->mergerManager->mergeItems($dsName, $data);
            $dataSource['data']['totals'][0] = $this->mergerManager->mergeTotals($dsName, $data);

            unset(
                $dataSource['data']['compare_items'],
                $dataSource['data']['compare_totals'],
                $dataSource['data']['number_columns']
            );
        }

        return parent::prepareDataSource($dataSource);
    }
}

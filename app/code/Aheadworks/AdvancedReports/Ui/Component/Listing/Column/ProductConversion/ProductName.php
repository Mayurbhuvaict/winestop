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

































































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\ProductConversion;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;
use Aheadworks\AdvancedReports\Model\Url\Base64Coder;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Class ProductName
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\ProductConversion
 */
class ProductName extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlModel $urlModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlModel $urlModel,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlModel = $urlModel;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $groupBy = $dataSource['data']['groupByFilter'];
            foreach ($dataSource['data']['items'] as &$item) {
                $params = (array)$this->context->getDataProvider()->getAllowedRequestParams();
                $params['group_by'] = $this->getGroupByValue($groupBy);
                $params['product_id'] = $item['product_id'];
                $params['product_name'] = Base64Coder::encode($item['product_name']);
                $item['row_url'] = $this->urlModel->getUrl(
                    'productconversion',
                    'productconversion_variant',
                    $dataSource['data']['periodFromFilter'],
                    $dataSource['data']['periodToFilter'],
                    $params
                );
                $item['row_label'] = $item['product_name'];
            }
        }
        return $dataSource;
    }

    /**
     * Get "group_by" parameter value
     *
     * @param string $groupBy
     * @return string
     */
    private function getGroupByValue($groupBy)
    {
        switch ($groupBy) {
            case GroupbySource::TYPE_DAY:
            case GroupbySource::TYPE_WEEK:
                $value = GroupbySource::TYPE_DAY;
                break;
            case GroupbySource::TYPE_MONTH:
                $value = GroupbySource::TYPE_WEEK;
                break;
            case GroupbySource::TYPE_QUARTER:
                $value = GroupbySource::TYPE_MONTH;
                break;
            case GroupbySource::TYPE_YEAR:
                $value = GroupbySource::TYPE_QUARTER;
                break;
            default:
                $value = GroupbySource::TYPE_MONTH;
        }
        return $value;
    }
}

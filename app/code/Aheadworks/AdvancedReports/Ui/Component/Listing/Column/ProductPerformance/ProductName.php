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


































































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\ProductPerformance;

use Aheadworks\AdvancedReports\Model\Url\Base64Coder;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;

/**
 * Class ProductName
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\ProductPerformance
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
            foreach ($dataSource['data']['items'] as &$item) {
                $params = (array)$this->context->getDataProvider()->getAllowedRequestParams();
                $params['product_id'] = $item['product_id'];
                $params['product_name'] = Base64Coder::encode($item['product_name']);

                if (isset($item['parent_id']) && $item['parent_id']) {
                    $params['parent_id'] = $item['parent_id'];
                }

                $item['row_url'] = $this->urlModel->getUrl(
                    'productperformance',
                    'productperformance_variantperformance',
                    $dataSource['data']['periodFromFilter'],
                    $dataSource['data']['periodToFilter'],
                    $params
                );
                $item['row_label'] = $item['product_name'];
            }
        }
        return $dataSource;
    }
}

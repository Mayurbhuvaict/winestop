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
































































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\Location;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;
use Aheadworks\AdvancedReports\Model\Source\Country as CountrySource;

/**
 * Class Country
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\Location
 */
class Country extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @var CountrySource
     */
    private $countrySource;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlModel $urlModel
     * @param CountrySource $countrySource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlModel $urlModel,
        CountrySource $countrySource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlModel = $urlModel;
        $this->countrySource = $countrySource;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['row_label'] = $this->countrySource->getOptionByValue($item['country_id']);
                $params = [
                    'country_id' => $item['country_id'],
                    'country_name' => base64_encode($item['row_label'])
                ];
                $item['row_url'] = $this->urlModel->getUrl(
                    'location',
                    'location_region',
                    $dataSource['data']['periodFromFilter'],
                    $dataSource['data']['periodToFilter'],
                    $params
                );
            }
        }
        return $dataSource;
    }
}

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










































































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Chart\Region;

use Aheadworks\AdvancedReports\Model\Config;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Ui\DataProvider\Compare\Merger\Manager as CompareMergerManager;

/**
 * Class Chart
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Chart\Region
 */
class Chart extends \Aheadworks\AdvancedReports\Ui\Component\Listing\Chart\Chart
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContextInterface $context
     * @param CompareMergerManager $mergerManager
     * @param Config $config
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        CompareMergerManager $mergerManager,
        Config $config,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $mergerManager, $components, $data);
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        $countryId = $this->getContext()->getRequestParam('country_id');
        $countries = $this->config->getCountriesWithStateRequired();
        if (in_array($countryId, $countries)) {
            if ($countryId) {
                $dataSource['data']['chart']['options'] = [
                    'region' => $countryId,
                    'resolution' => 'provinces'
                ];
            }
        }

        return $dataSource;
    }
}

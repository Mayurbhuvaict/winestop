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















































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Toolbar;

use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Ui\Component\OptionsContainer;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\Period\RangeResolver as PeriodRangeResolver;

/**
 * Class PeriodType
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Toolbar
 */
class PeriodType extends OptionsContainer
{
    /**
     * @var PeriodRangeResolver
     */
    private $periodRangeResolver;

    /**
     * @param ContextInterface $context
     * @param PeriodRangeResolver $periodRangeResolver
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        PeriodRangeResolver $periodRangeResolver,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->periodRangeResolver = $periodRangeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $periodFilter = $this->context->getDataProvider()->getDefaultFilterPool()->getFilter('period');
        $config = $this->getData('config');
        $config['currentValue'] = $periodFilter->getPeriodType();
        $config['default'] = $periodFilter->getDefaultPeriodType();
        $this->setData('config', $config);

        $this->preparePeriodOptions();
    }

    /**
     * Prepare period options
     *
     * @return $this
     */
    private function preparePeriodOptions()
    {
        $config = $this->getData('config');
        $options = $config['options'];
        foreach ($options as &$option) {
            $periodType = $option['value'];
            $periods = $this->periodRangeResolver->resolve($periodType);

            $option['period'] = $this->preparePeriod($periodType, $periods['from'], $periods['to']);
            $option['comparePeriod'] = $this->preparePeriod($periodType, $periods['c_from'], $periods['c_to']);
        }
        $config['options'] = $options;
        $this->setData('config', $config);

        return $this;
    }

    /**
     * Prepare period
     *
     * @param string $periodType
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @return string
     */
    private function preparePeriod($periodType, $periodFrom, $periodTo)
    {
        if (in_array($periodType, [PeriodSource::TYPE_TODAY, PeriodSource::TYPE_YESTERDAY])) {
            return $periodFrom->format('F d, Y');
        }

        return $periodFrom->format('M d') . ' - ' . $periodTo->format('M d, Y');
    }
}

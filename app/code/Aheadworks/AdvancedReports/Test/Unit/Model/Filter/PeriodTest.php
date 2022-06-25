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














































































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Model\Filter;

use Aheadworks\AdvancedReports\Model\Filter\Period;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Source\Compare as CompareSource;
use Aheadworks\AdvancedReports\Ui\DataProvider\Filters\DefaultFilter\Period\RangeResolver as PeriodRangeResolver;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Filter\Period
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PeriodTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Period
     */
    private $model;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var PeriodRangeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $periodRangeResolverMock;

    /**
     * @var SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->periodRangeResolverMock = $this->getMockBuilder(PeriodRangeResolver::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'resolve'
                ]
            )->getMock();
        $this->sessionMock = $this->getMockForAbstractClass(
            SessionManagerInterface::class,
            [],
            '',
            true,
            true,
            true,
            [
                'setData',
                'getData'
            ]
        );
        $this->localeDateMock = $this->getMockForAbstractClass(TimezoneInterface::class);
        $this->model = $objectManager->getObject(
            Period::class,
            [
                'request' => $this->requestMock,
                'periodRangeResolver' => $this->periodRangeResolverMock,
                'session' => $this->sessionMock,
                'localeDate' => $this->localeDateMock,
            ]
        );
    }

    /**
     * Testing of getDefaultValue method
     */
    public function testGetDefaultValue()
    {
        $expected = [
            'is_this_month_forecast_enabled' => null,
            'type' => PeriodSource::TYPE_MONTH_TO_DATE,
            'from' => null,
            'to' => null,
            'is_compare_enabled' => false,
            'compare_type' => CompareSource::TYPE_PREVIOUS_PERIOD,
            'compare_from' => null,
            'compare_to' => null,
        ];

        $this->assertEquals($expected, $this->model->getDefaultValue());
    }

    /**
     * Testing of getValue method
     */
    public function testGetValue()
    {
        $defaultConfigTimezoneCode = 'UTC';
        $defaultConfigTimezone = new \DateTimeZone($defaultConfigTimezoneCode);
        $dateFrom = new \DateTime('2018-01-01 00:00:01.000000', $defaultConfigTimezone);
        $dateTo = new \DateTime('2018-01-15 00:00:01.000000', $defaultConfigTimezone);
        $expected = [
            'is_this_month_forecast_enabled' => false,
            'type' => PeriodSource::TYPE_MONTH_TO_DATE,
            'from' => $dateFrom,
            'to' => $dateTo,
            'is_compare_enabled' => false,
            'compare_type' => CompareSource::TYPE_PREVIOUS_PERIOD,
            'compare_from' => null,
            'compare_to' => null,
        ];

        $this->localeDateMock->expects($this->any())
            ->method('getConfigTimezone')
            ->with(ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn($defaultConfigTimezoneCode);

        $range = [
            'from'   => $dateFrom,
            'to'     => $dateTo,
        ];
        $this->periodRangeResolverMock->expects($this->any())
            ->method('resolve')
            ->with(PeriodSource::TYPE_MONTH_TO_DATE)
            ->willReturn($range);

        $tmp = $this->model->getValue();
        $this->assertEquals($expected, $tmp);
    }
}

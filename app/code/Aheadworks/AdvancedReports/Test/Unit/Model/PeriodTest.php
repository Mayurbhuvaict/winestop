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




































































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Model;

use Aheadworks\AdvancedReports\Model\Period;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Aheadworks\AdvancedReports\Model\Source\Groupby;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;
use \Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\AbstractResource;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Period
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PeriodTest extends \PHPUnit\Framework\TestCase
{
    private $defaultConfigTimezoneCode = 'UTC';
    /**
     * @var Period
     */
    private $model;

    /**
     * @var DatesGroupingFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $datesGroupingFactoryMock;

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
        $this->datesGroupingFactoryMock = $this->getMockBuilder(DatesGroupingFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->localeDateMock = $this->getMockBuilder(TimezoneInterface::class)
            ->setMethods(['getConfigTimezone'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->model = $objectManager->getObject(
            Period::class,
            [
                'datesGroupingFactory' => $this->datesGroupingFactoryMock,
                'localeDate' => $this->localeDateMock,
            ]
        );
    }

    /**
     * Testing of periodDatesResolve method
     * @dataProvider periodDatesResolveDataProvider
     *
     * @param array $item
     * @param string $groupBy
     * @param \DateTime $periodFrom
     * @param \DateTime $periodTo
     * @param bool $isCompare
     * @param array $expected
     */
    public function testPeriodDatesResolve($item, $groupBy, $periodFrom, $periodTo, $isCompare, $expected)
    {
        $this->localeDateMock->expects($this->once())
            ->method('getConfigTimezone')
            ->with(ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
            ->willReturn($this->defaultConfigTimezoneCode);

        $this->assertEquals(
            $expected,
            $this->model->periodDatesResolve($item, $groupBy, $periodFrom, $periodTo, $isCompare)
        );
    }

    /**
     * Data provider for testPeriodDatesResolve method
     *
     * @return array
     */
    public function periodDatesResolveDataProvider()
    {
        $dateTimeZone = new \DateTimeZone($this->defaultConfigTimezoneCode);
        return [
            [
                [
                    'date' => '2018-01-06',
                ],
                GroupbySource::TYPE_DAY,
                new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone),
                false,
                [
                    'start_date' => new \DateTime('2018-01-06 00:00:00.000000', $dateTimeZone),
                    'end_date' => new \DateTime('2018-01-06 00:00:00.000000', $dateTimeZone)
                ]
            ],
            [
                [
                    'test_key' => 'test_value',
                ],
                GroupbySource::TYPE_DAY,
                new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone),
                false,
                null
            ],
            [
                [
                    'start_date' => '2018-01-01',
                    'end_date' => '2018-01-31'
                ],
                GroupbySource::TYPE_WEEK,
                new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone),
                false,
                [
                    'start_date' => new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                    'end_date' => new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone)
                ]
            ],
            [
                [
                    'start_date' => '2018-01-01',
                    'end_date' => '2018-01-31'
                ],
                GroupbySource::TYPE_MONTH,
                new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone),
                false,
                [
                    'start_date' => new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                    'end_date' => new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone)
                ]
            ],
            [
                [
                    'start_date' => '2018-01-01',
                    'end_date' => '2018-01-31'
                ],
                GroupbySource::TYPE_QUARTER,
                new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone),
                false,
                [
                    'start_date' => new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                    'end_date' => new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone)
                ]
            ],
            [
                [
                    'start_date' => '2018-01-01',
                    'end_date' => '2018-01-31'
                ],
                GroupbySource::TYPE_YEAR,
                new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone),
                false,
                [
                    'start_date' => new \DateTime('2018-01-01 15:24:04.000000', $dateTimeZone),
                    'end_date' => new \DateTime('2018-01-16 15:24:04.000000', $dateTimeZone)
                ]
            ],
        ];
    }

    /**
     * Testing of getPeriodAsString method
     * @dataProvider getPeriodAsStringDataProvider
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $groupType
     * @param string $expected
     */
    public function testGetPeriodAsString($from, $to, $groupType, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->model->getPeriodAsString($from, $to, $groupType)
        );
    }

    /**
     * Data provider for testGetPeriodAsString method
     *
     * @return array
     */
    public function getPeriodAsStringDataProvider()
    {
        return [
            [new \DateTime('2016-12-12'), new \DateTime('2016-12-12'), Groupby::TYPE_DAY, 'Dec 12, 2016'],
            [
                new \DateTime('2016-06-01'), new \DateTime('2016-06-04'),
                Groupby::TYPE_WEEK,
                'Jun 01, 2016 - Jun 04, 2016'
            ],
            [new \DateTime('2016-12-12'), new \DateTime('2016-12-30'), Groupby::TYPE_MONTH, 'Dec 2016'],
            [new \DateTime('2016-07-01'), new \DateTime('2016-09-30'), Groupby::TYPE_QUARTER, 'Q3 2016'],
            [new \DateTime('2016-12-12'), new \DateTime('2016-12-12'), Groupby::TYPE_YEAR, '2016']
        ];
    }

    /**
     * Testing of getPeriods method with exception throwing
     */
    public function testGetPeriodsException()
    {
        $from = new \DateTime('2018-01-01');
        $intervalsCount = 5;
        $groupBy = Groupby::TYPE_DAY;
        $expected = ['period' => $groupBy, 'intervals' => []];
        $this->datesGroupingFactoryMock->expects($this->once())
            ->method('create')
            ->with($groupBy)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->assertEquals($expected, $this->model->getPeriods($from, $intervalsCount, $groupBy));
    }

    /**
     * Testing of getPeriods method with exception throwing on intervals getting
     */
    public function testGetPeriodsGettingIntervalsWithException()
    {
        $from = new \DateTime('2018-01-01');
        $intervalsCount = 5;
        $groupBy = Groupby::TYPE_DAY;
        $expected = ['period' => $groupBy, 'intervals' => []];

        $datePeriodMock = $this->getMockBuilder(AbstractResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getPeriods'
                ]
            )->getMockForAbstractClass();
        $datePeriodMock->expects($this->once())
            ->method('getPeriods')
            ->with($from, $intervalsCount)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->datesGroupingFactoryMock->expects($this->once())
            ->method('create')
            ->with($groupBy)
            ->willReturn($datePeriodMock);

        $this->assertEquals($expected, $this->model->getPeriods($from, $intervalsCount, $groupBy));
    }

    /**
     * Testing of getPeriods method
     */
    public function testGetPeriods()
    {
        $from = new \DateTime('2018-01-01');
        $intervalsCount = 3;
        $groupBy = Groupby::TYPE_DAY;
        $intervals = [
            ['date' => '2018-01-01'],
            ['date' => '2018-01-02'],
            ['date' => '2018-01-03'],
        ];
        $expected = ['period' => $groupBy, 'intervals' => $intervals];

        $datePeriodMock = $this->getMockBuilder(AbstractResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getPeriods'
                ]
            )->getMockForAbstractClass();
        $datePeriodMock->expects($this->once())
            ->method('getPeriods')
            ->with($from, $intervalsCount)
            ->willReturn($intervals);

        $this->datesGroupingFactoryMock->expects($this->once())
            ->method('create')
            ->with($groupBy)
            ->willReturn($datePeriodMock);

        $this->assertEquals($expected, $this->model->getPeriods($from, $intervalsCount, $groupBy));
    }
}

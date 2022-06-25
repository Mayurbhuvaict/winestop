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

use Aheadworks\AdvancedReports\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CacheInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Day as DatesGroupingDay;
use Magento\Framework\Exception\LocalizedException;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Config
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * @var CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheMock;

    /**
     * @var DatesGroupingFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $datesGroupingFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getValue'
                ]
            )->getMockForAbstractClass();

        $this->cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'load',
                    'save'
                ]
            )->getMockForAbstractClass();

        $this->datesGroupingFactoryMock = $this->getMockBuilder(DatesGroupingFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'create',
                ]
            )->getMock();

        $data = [
            'scopeConfig' => $this->scopeConfigMock,
            'cache' => $this->cacheMock,
            'datesGroupingFactory' => $this->datesGroupingFactoryMock,
        ];
        $this->model = $objectManager->getObject(Config::class, $data);
    }

    /**
     * Testing of getOrderStatus method
     */
    public function testGetOrderStatus()
    {
        $value = 'complete';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_ORDER_STATUS)
            ->willReturn($value);
        $this->assertSame($value, $this->model->getOrderStatus());
    }

    /**
     * Testing of getManufacturerAttribute method
     */
    public function testGetManufacturerAttribute()
    {
        $value = 'manufacturer';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_MANUFACTURER_ATTRIBUTE)
            ->willReturn($value);
        $this->assertSame($value, $this->model->getManufacturerAttribute());
    }

    /**
     * Testing of getFirstDayOfWeek method
     */
    public function testGetFirstDayOfWeek()
    {
        $value = 'Sunday';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_LOCALE_FIRSTDAY)
            ->willReturn($value);
        $this->assertSame($value, $this->model->getFirstDayOfWeek());
    }

    /**
     * Testing of getCountriesWithStateRequired method
     */
    public function testGetCountriesWithStateRequired()
    {
        $value = 'AT,BR,CA,CH,EE,ES,FI,HR,LT,LV,RO,US';
        $expected = [
            'AT',
            'BR',
            'CA',
            'CH',
            'EE',
            'ES',
            'FI',
            'HR',
            'LT',
            'LV',
            'RO',
            'US',
        ];

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_REGION_STATE_REQUIRED)
            ->willReturn($value);
        $this->assertSame($expected, $this->model->getCountriesWithStateRequired());
    }

    /**
     * Testing of getFirstAvailableDate method with loading the value from cache
     */
    public function testGetFirstAvailableDateFromCache()
    {
        $minDate = '2018-01-01';

        $this->cacheMock->expects($this->once())
            ->method('load')
            ->with(Config::MIN_DATE_CACHE_KEY)
            ->willReturn($minDate);
        $this->assertSame($minDate, $this->model->getFirstAvailableDate());
    }

    /**
     * Testing of getFirstAvailableDate method with loading the value from database
     */
    public function testGetFirstAvailableDateFromDatabase()
    {
        $expected = $minDate = '2018-01-01';

        $this->cacheMock->expects($this->once())
            ->method('load')
            ->with(Config::MIN_DATE_CACHE_KEY)
            ->willReturn(null);

        $dateGroupingObjectMock = $this->getMockBuilder(DatesGroupingDay::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getMinDate'
                ]
            )->getMock();
        $dateGroupingObjectMock->expects($this->once())
            ->method('getMinDate')
            ->willReturn($minDate);

        $this->datesGroupingFactoryMock->expects($this->once())
            ->method('create')
            ->with(DatesGroupingDay::KEY)
            ->willReturn($dateGroupingObjectMock);

        $this->cacheMock->expects($this->once())
            ->method('save')
            ->with($minDate, Config::MIN_DATE_CACHE_KEY, [], null)
            ->willReturn(true);

        $this->assertSame($expected, $this->model->getFirstAvailableDate());
    }

    /**
     * Testing of getFirstAvailableDate method with loading the value from database
     * and processing the exception connected to creating object in the factory
     */
    public function testGetFirstAvailableDateFromDatabaseExceptionOnCreatingObject()
    {
        $expected = '';

        $this->cacheMock->expects($this->once())
            ->method('load')
            ->with(Config::MIN_DATE_CACHE_KEY)
            ->willReturn(null);

        $this->datesGroupingFactoryMock->expects($this->once())
            ->method('create')
            ->with(DatesGroupingDay::KEY)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->cacheMock->expects($this->never())
            ->method('save');

        $this->assertSame($expected, $this->model->getFirstAvailableDate());
    }

    /**
     * Testing of getFirstAvailableDate method with loading the value from database
     * and processing the exception connected to getting value from the corresponding object
     */
    public function testGetFirstAvailableDateFromDatabaseExceptionOnGettingValue()
    {
        $expected = '';

        $this->cacheMock->expects($this->once())
            ->method('load')
            ->with(Config::MIN_DATE_CACHE_KEY)
            ->willReturn(null);

        $dateGroupingObjectMock = $this->getMockBuilder(DatesGroupingDay::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getMinDate'
                ]
            )->getMock();
        $dateGroupingObjectMock->expects($this->once())
            ->method('getMinDate')
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->datesGroupingFactoryMock->expects($this->once())
            ->method('create')
            ->with(DatesGroupingDay::KEY)
            ->willReturn($dateGroupingObjectMock);

        $this->cacheMock->expects($this->never())
            ->method('save');

        $this->assertSame($expected, $this->model->getFirstAvailableDate());
    }
}

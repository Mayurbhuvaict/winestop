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

use Aheadworks\AdvancedReports\Model\Filter\Store;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReports\Model\Filter\Store\Encoder as FilterStoreEncoder;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Filter\Store
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Store
     */
    private $model;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var FilterStoreEncoder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterStoreEncoderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->sessionMock = $this->getMockForAbstractClass(
            SessionManagerInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['setData', 'getData']
        );
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->filterStoreEncoderMock = $this->getMockBuilder(FilterStoreEncoder::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'encode',
                    'decode',
                ]
            )->getMock();
        $this->model = $objectManager->getObject(
            Store::class,
            [
                'session' => $this->sessionMock,
                'storeManager' => $this->storeManagerMock,
                'filterStoreEncoder' => $this->filterStoreEncoderMock,
                'request' => $this->requestMock,
            ]
        );
    }

    /**
     * Testing of getWebsiteId method
     */
    public function testGetWebsiteIdFromDefault()
    {
        $defaultValueEncoded = Store::DEFAULT_TYPE . FilterStoreEncoder::DELIMITER . Store::DEFAULT_TYPE;
        $defaultValueDecoded = [
            Store::DEFAULT_TYPE,
            Store::DEFAULT_TYPE
        ];
        $expected = 0;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn(null);
        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with(Store::SESSION_KEY)
            ->willReturn(null);
        $this->filterStoreEncoderMock->expects($this->once())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE)
            ->willReturn($defaultValueEncoded);
        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($defaultValueEncoded)
            ->willReturn($defaultValueDecoded);
        $this->assertEquals($expected, $this->model->getWebsiteId());
    }

    /**
     * Testing of getWebsiteId method
     */
    public function testGetWebsiteIdFromRequestDefault()
    {
        $valueEncoded = Store::DEFAULT_TYPE . FilterStoreEncoder::DELIMITER . Store::DEFAULT_TYPE;
        $valueDecoded = [
            Store::DEFAULT_TYPE,
            Store::DEFAULT_TYPE
        ];
        $expected = 0;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn($valueEncoded);

        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->never())
            ->method('getData')
            ->with(Store::SESSION_KEY);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);
        $this->assertEquals($expected, $this->model->getWebsiteId());
    }

    /**
     * Testing of getWebsiteId method
     */
    public function testGetWebsiteIdFromRequestWebsite()
    {
        $valueEncoded = Store::WEBSITE_TYPE . FilterStoreEncoder::DELIMITER . '1';
        $valueDecoded = [
            Store::WEBSITE_TYPE,
            '1'
        ];
        $expected = 4;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn($valueEncoded);

        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->never())
            ->method('getData')
            ->with(Store::SESSION_KEY);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);

        $websiteMock = $this->getMockBuilder(WebsiteInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $websiteMock->expects($this->once())
            ->method('getId')
            ->willReturn($expected);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with($valueDecoded[1])
            ->willReturn($websiteMock);

        $this->assertEquals($expected, $this->model->getWebsiteId());
    }

    /**
     * Testing of getWebsiteId method
     */
    public function testGetWebsiteIdFromRequestGroup()
    {
        $valueEncoded = Store::GROUP_TYPE . FilterStoreEncoder::DELIMITER . '1';
        $valueDecoded = [
            Store::GROUP_TYPE,
            '1'
        ];
        $expected = 4;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn($valueEncoded);

        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->never())
            ->method('getData')
            ->with(Store::SESSION_KEY);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);

        $groupMock = $this->getMockBuilder(GroupInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId'])
            ->getMockForAbstractClass();
        $groupMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($expected);
        $this->storeManagerMock->expects($this->once())
            ->method('getGroup')
            ->with($valueDecoded[1])
            ->willReturn($groupMock);

        $this->assertEquals($expected, $this->model->getWebsiteId());
    }

    /**
     * Testing of getWebsiteId method
     */
    public function testGetWebsiteIdFromRequestStore()
    {
        $valueEncoded = Store::STORE_TYPE . FilterStoreEncoder::DELIMITER . '1';
        $valueDecoded = [
            Store::STORE_TYPE,
            '1'
        ];
        $expected = 4;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn($valueEncoded);

        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->never())
            ->method('getData')
            ->with(Store::SESSION_KEY);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($expected);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($valueDecoded[1])
            ->willReturn($storeMock);

        $this->assertEquals($expected, $this->model->getWebsiteId());
    }

    /**
     * Testing of getWebsiteId method
     */
    public function testGetWebsiteIdFromSession()
    {
        $valueEncoded = Store::DEFAULT_TYPE . FilterStoreEncoder::DELIMITER . Store::DEFAULT_TYPE;
        $valueDecoded = [
            Store::DEFAULT_TYPE,
            Store::DEFAULT_TYPE
        ];
        $expected = 0;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn(null);

        $this->sessionMock->expects($this->never())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with(Store::SESSION_KEY)
            ->willReturn($valueEncoded);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);
        $this->assertEquals($expected, $this->model->getWebsiteId());
    }

    /**
     * Testing of getStoreIds method
     */
    public function testGetStoreIdsFromDefault()
    {
        $defaultValueEncoded = Store::DEFAULT_TYPE . FilterStoreEncoder::DELIMITER . Store::DEFAULT_TYPE;
        $defaultValueDecoded = [
            Store::DEFAULT_TYPE,
            Store::DEFAULT_TYPE
        ];
        $expected = null;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn(null);
        $this->sessionMock->expects($this->once())
            ->method('getData')
            ->with(Store::SESSION_KEY)
            ->willReturn(null);
        $this->filterStoreEncoderMock->expects($this->once())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE)
            ->willReturn($defaultValueEncoded);
        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($defaultValueEncoded)
            ->willReturn($defaultValueDecoded);
        $this->assertEquals($expected, $this->model->getStoreIds());
    }

    /**
     * Testing of getStoreIds method
     */
    public function testGetStoreIdsFromRequestWebsite()
    {
        $valueEncoded = Store::WEBSITE_TYPE . FilterStoreEncoder::DELIMITER . '1';
        $valueDecoded = [
            Store::WEBSITE_TYPE,
            '1'
        ];
        $expected = ['1', '2'];
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn($valueEncoded);

        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->never())
            ->method('getData')
            ->with(Store::SESSION_KEY);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);

        $websiteMock = $this->getMockBuilder(WebsiteInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStoreIds'])
            ->getMockForAbstractClass();
        $websiteMock->expects($this->once())
            ->method('getStoreIds')
            ->willReturn($expected);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->with($valueDecoded[1])
            ->willReturn($websiteMock);

        $this->assertEquals($expected, $this->model->getStoreIds());
    }

    /**
     * Testing of getStoreIds method
     */
    public function testGetStoreIdsFromRequestGroup()
    {
        $valueEncoded = Store::GROUP_TYPE . FilterStoreEncoder::DELIMITER . '1';
        $valueDecoded = [
            Store::GROUP_TYPE,
            '1'
        ];
        $expected = ['1', '2'];
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn($valueEncoded);

        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->never())
            ->method('getData')
            ->with(Store::SESSION_KEY);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);

        $groupMock = $this->getMockBuilder(GroupInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStoreIds'])
            ->getMockForAbstractClass();
        $groupMock->expects($this->once())
            ->method('getStoreIds')
            ->willReturn($expected);
        $this->storeManagerMock->expects($this->once())
            ->method('getGroup')
            ->with($valueDecoded[1])
            ->willReturn($groupMock);

        $this->assertEquals($expected, $this->model->getStoreIds());
    }

    /**
     * Testing of getStoreIds method
     */
    public function testGetStoreIdsFromRequestStore()
    {
        $storeId = '1';
        $valueEncoded = Store::STORE_TYPE . FilterStoreEncoder::DELIMITER . $storeId;
        $valueDecoded = [
            Store::STORE_TYPE,
            $storeId
        ];
        $expected = [$storeId];
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('report_scope')
            ->willReturn($valueEncoded);

        $this->sessionMock->expects($this->once())
            ->method('setData')
            ->with(Store::SESSION_KEY, $valueEncoded)
            ->willReturnSelf();
        $this->sessionMock->expects($this->never())
            ->method('getData')
            ->with(Store::SESSION_KEY);

        $this->filterStoreEncoderMock->expects($this->never())
            ->method('encode')
            ->with(Store::DEFAULT_TYPE, Store::DEFAULT_TYPE);

        $this->filterStoreEncoderMock->expects($this->once())
            ->method('decode')
            ->with($valueEncoded)
            ->willReturn($valueDecoded);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($valueDecoded[1])
            ->willReturn($storeMock);

        $this->assertEquals($expected, $this->model->getStoreIds());
    }
}

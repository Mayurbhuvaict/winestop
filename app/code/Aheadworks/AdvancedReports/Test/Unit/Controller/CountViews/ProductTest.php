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



















































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Controller\CountViews;

use Aheadworks\AdvancedReports\Controller\CountViews\Product as ProductController;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Model\Log\ProductViewFactory as ProductViewLogFactory;
use Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductView as ProductViewLogResource;
use Aheadworks\AdvancedReports\Model\ResourceModel\Log\ProductViewFactory as ProductViewLogResourceFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Customer\Model\Visitor;
use Magento\Customer\Model\VisitorFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Test for \Aheadworks\AdvancedReports\Controller\CountViews\Product
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ProductController
     */
    private $controller;

    /**
     * @var SessionManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionManagerMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var VisitorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $visitorFactoryMock;

    /**
     * @var ProductViewLogFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productViewLogFactoryMock;

    /**
     * @var ProductViewLogResourceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productViewLogResourceFactoryMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->setMethods(
                [
                    'getParam',
                    'isAjax'
                ]
            )->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
            ]
        );

        $this->sessionManagerMock = $this->getMockBuilder(SessionManagerInterface::class)
            ->setMethods(['getVisitorData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->setMethods(['getStore'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->visitorFactoryMock = $this->getMockBuilder(VisitorFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->productViewLogResourceFactoryMock = $this->getMockBuilder(ProductViewLogResourceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->productViewLogFactoryMock = $this->getMockBuilder(ProductViewLogFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->customerRepositoryMock = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->controller = $objectManager->getObject(
            ProductController::class,
            [
                'context' => $contextMock,
                'sessionManager' => $this->sessionManagerMock,
                'storeManager' => $this->storeManagerMock,
                'visitorFactory' => $this->visitorFactoryMock,
                'productViewLogResourceFactory' => $this->productViewLogResourceFactoryMock,
                'productViewLogFactory' => $this->productViewLogFactoryMock,
                'customerRepository' => $this->customerRepositoryMock,
            ]
        );
    }

    /**
     * Testing of execute method with non-ajax request
     */
    public function testExecuteNonAjaxRequest()
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(null);

        $this->requestMock->expects($this->never())
            ->method('getParam')
            ->with('id');

        $this->controller->execute();
    }

    /**
     * Testing of execute method without necessary data
     *
     * @dataProvider dataProviderExecuteAjaxRequestWithoutNecessaryData
     * @param int $productId
     * @param int $storeId
     * @param array $visitorData
     */
    public function testExecuteAjaxRequestWithoutNecessaryData($productId, $storeId, $visitorData)
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($productId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->sessionManagerMock->expects($this->once())
            ->method('getVisitorData')
            ->willReturn($visitorData);

        $this->visitorFactoryMock->expects($this->never())
            ->method('create');

        $this->controller->execute();
    }

    /**
     * Retrieve data provider for testExecuteAjaxRequestWithoutNecessaryData test
     *
     * @return array
     */
    public function dataProviderExecuteAjaxRequestWithoutNecessaryData()
    {
        return [
            [null, 1, ['visitor data']],
            [1, null, ['visitor data']],
            [1, 2, null],
            [1, 2, []],
            [null, null, ['visitor data']],
            [1, null, null],
            [null, null, null],
        ];
    }

    /**
     * Testing of execute method when visitor has no id
     */
    public function testExecuteVisitorWithoutId()
    {
        $productId = 1;
        $storeId = 2;
        $visitorData = [
            'last_visit_at' => '2018-01-01 00:00:01',
            'session_id' => 'session_unique_id',
            'visitor_id' => '1',

        ];
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($productId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->sessionManagerMock->expects($this->once())
            ->method('getVisitorData')
            ->willReturn($visitorData);

        $visitorMock = $this->getMockBuilder(Visitor::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setData',
                    'getId',
                    'getCustomerId',
                ]
            )->getMock();
        $visitorMock->expects($this->once())
            ->method('setData')
            ->with($visitorData)
            ->willReturnSelf();
        $visitorMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->visitorFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($visitorMock);

        $this->productViewLogResourceFactoryMock->expects($this->never())
            ->method('create');

        $this->controller->execute();
    }

    /**
     * Testing of execute method when corresponding log record exists
     */
    public function testExecuteLogRecordExists()
    {
        $productId = 1;
        $storeId = 2;
        $visitorData = [
            'last_visit_at' => '2018-01-01 00:00:01',
            'session_id' => 'session_unique_id',
            'visitor_id' => '1',

        ];
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($productId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->sessionManagerMock->expects($this->once())
            ->method('getVisitorData')
            ->willReturn($visitorData);

        $visitorMock = $this->getMockBuilder(Visitor::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setData',
                    'getId',
                    'getCustomerId',
                ]
            )->getMock();
        $visitorMock->expects($this->once())
            ->method('setData')
            ->with($visitorData)
            ->willReturnSelf();
        $visitorMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($visitorData['visitor_id']);
        $this->visitorFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($visitorMock);

        $logResourceMock = $this->getMockBuilder(ProductViewLogResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isExist',
                    'save'
                ]
            )->getMock();
        $logResourceMock->expects($this->once())
            ->method('isExist')
            ->with($productId, $visitorData['visitor_id'])
            ->willReturn(true);
        $this->productViewLogResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($logResourceMock);

        $this->productViewLogFactoryMock->expects($this->never())
            ->method('create');

        $this->controller->execute();
    }

    /**
     * Testing of execute method for guest user
     */
    public function testExecuteForGuest()
    {
        $productId = 1;
        $storeId = 2;
        $visitorData = [
            'last_visit_at' => '2018-01-01 00:00:01',
            'session_id' => 'session_unique_id',
            'visitor_id' => '1',

        ];
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($productId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->sessionManagerMock->expects($this->once())
            ->method('getVisitorData')
            ->willReturn($visitorData);

        $visitorMock = $this->getMockBuilder(Visitor::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setData',
                    'getId',
                    'getCustomerId',
                ]
            )->getMock();
        $visitorMock->expects($this->once())
            ->method('setData')
            ->with($visitorData)
            ->willReturnSelf();
        $visitorMock->expects($this->exactly(3))
            ->method('getId')
            ->willReturn($visitorData['visitor_id']);
        $this->visitorFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($visitorMock);

        $logResourceMock = $this->getMockBuilder(ProductViewLogResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isExist',
                    'save'
                ]
            )->getMock();
        $logResourceMock->expects($this->once())
            ->method('isExist')
            ->with($productId, $visitorData['visitor_id'])
            ->willReturn(false);
        $this->productViewLogResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($logResourceMock);

        $productViewLogMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setProductId',
                    'setVisitorId',
                    'setStoreId',
                    'setCustomerId',
                    'setCustomerGroupId',
                ]
            )->getMock();
        $productViewLogMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setVisitorId')
            ->with($visitorData['visitor_id'])
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $this->productViewLogFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productViewLogMock);

        $visitorMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn(null);

        $this->customerRepositoryMock->expects($this->never())
            ->method('create');

        $logResourceMock->expects($this->once())
            ->method('save')
            ->with($productViewLogMock)
            ->willReturnSelf();

        $this->controller->execute();
    }

    /**
     * Testing of execute method for registered customer
     */
    public function testExecuteForRegisteredCustomer()
    {
        $productId = 1;
        $storeId = 2;
        $visitorData = [
            'last_visit_at' => '2018-01-01 00:00:01',
            'session_id' => 'session_unique_id',
            'visitor_id' => '2',
            'do_customer_login' => '1',
            'customer_id' => '2',

        ];
        $customerGroupId = 3;
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($productId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->sessionManagerMock->expects($this->once())
            ->method('getVisitorData')
            ->willReturn($visitorData);

        $visitorMock = $this->getMockBuilder(Visitor::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setData',
                    'getId',
                    'getCustomerId',
                ]
            )->getMock();
        $visitorMock->expects($this->once())
            ->method('setData')
            ->with($visitorData)
            ->willReturnSelf();
        $visitorMock->expects($this->exactly(3))
            ->method('getId')
            ->willReturn($visitorData['visitor_id']);
        $this->visitorFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($visitorMock);

        $logResourceMock = $this->getMockBuilder(ProductViewLogResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isExist',
                    'save'
                ]
            )->getMock();
        $logResourceMock->expects($this->once())
            ->method('isExist')
            ->with($productId, $visitorData['visitor_id'])
            ->willReturn(false);
        $this->productViewLogResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($logResourceMock);

        $productViewLogMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setProductId',
                    'setVisitorId',
                    'setStoreId',
                    'setCustomerId',
                    'setCustomerGroupId',
                ]
            )->getMock();
        $productViewLogMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setVisitorId')
            ->with($visitorData['visitor_id'])
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $this->productViewLogFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productViewLogMock);

        $visitorMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->willReturn($visitorData['customer_id']);

        $customerMock = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getId',
                    'getGroupId',
                ]
            )->getMockForAbstractClass();
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($visitorData['customer_id']);
        $customerMock->expects($this->once())
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($visitorData['customer_id'])
            ->willReturn($customerMock);

        $productViewLogMock->expects($this->once())
            ->method('setCustomerId')
            ->with($visitorData['customer_id'])
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->willReturnSelf();

        $logResourceMock->expects($this->once())
            ->method('save')
            ->with($productViewLogMock)
            ->willReturnSelf();

        $this->controller->execute();
    }

    /**
     * Testing of execute method for registered customer with exception throwing on getting customer object by id
     */
    public function testExecuteForRegisteredCustomerWithException()
    {
        $productId = 1;
        $storeId = 2;
        $visitorData = [
            'last_visit_at' => '2018-01-01 00:00:01',
            'session_id' => 'session_unique_id',
            'visitor_id' => '2',
            'do_customer_login' => '1',
            'customer_id' => '2',

        ];
        $customerGroupId = 3;
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($productId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->sessionManagerMock->expects($this->once())
            ->method('getVisitorData')
            ->willReturn($visitorData);

        $visitorMock = $this->getMockBuilder(Visitor::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setData',
                    'getId',
                    'getCustomerId',
                ]
            )->getMock();
        $visitorMock->expects($this->once())
            ->method('setData')
            ->with($visitorData)
            ->willReturnSelf();
        $visitorMock->expects($this->exactly(3))
            ->method('getId')
            ->willReturn($visitorData['visitor_id']);
        $this->visitorFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($visitorMock);

        $logResourceMock = $this->getMockBuilder(ProductViewLogResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isExist',
                    'save'
                ]
            )->getMock();
        $logResourceMock->expects($this->once())
            ->method('isExist')
            ->with($productId, $visitorData['visitor_id'])
            ->willReturn(false);
        $this->productViewLogResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($logResourceMock);

        $productViewLogMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setProductId',
                    'setVisitorId',
                    'setStoreId',
                    'setCustomerId',
                    'setCustomerGroupId',
                ]
            )->getMock();
        $productViewLogMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setVisitorId')
            ->with($visitorData['visitor_id'])
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $this->productViewLogFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productViewLogMock);

        $visitorMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->willReturn($visitorData['customer_id']);

        $customerMock = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getId',
                    'getGroupId',
                ]
            )->getMockForAbstractClass();
        $customerMock->expects($this->never())
            ->method('getId')
            ->willReturn($visitorData['customer_id']);
        $customerMock->expects($this->never())
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($visitorData['customer_id'])
            ->willThrowException(new NoSuchEntityException(__('Error!')));

        $productViewLogMock->expects($this->never())
            ->method('setCustomerId')
            ->with($visitorData['customer_id'])
            ->willReturnSelf();
        $productViewLogMock->expects($this->never())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->willReturnSelf();

        $logResourceMock->expects($this->once())
            ->method('save')
            ->with($productViewLogMock)
            ->willReturnSelf();

        $this->controller->execute();
    }

    /**
     * Testing of execute method with throwing exception on log record saving
     */
    public function testExecuteWithExceptionOnRecordSaving()
    {
        $productId = 1;
        $storeId = 2;
        $visitorData = [
            'last_visit_at' => '2018-01-01 00:00:01',
            'session_id' => 'session_unique_id',
            'visitor_id' => '2',
            'do_customer_login' => '1',
            'customer_id' => '2',

        ];
        $customerGroupId = 3;
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($productId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->sessionManagerMock->expects($this->once())
            ->method('getVisitorData')
            ->willReturn($visitorData);

        $visitorMock = $this->getMockBuilder(Visitor::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setData',
                    'getId',
                    'getCustomerId',
                ]
            )->getMock();
        $visitorMock->expects($this->once())
            ->method('setData')
            ->with($visitorData)
            ->willReturnSelf();
        $visitorMock->expects($this->exactly(3))
            ->method('getId')
            ->willReturn($visitorData['visitor_id']);
        $this->visitorFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($visitorMock);

        $logResourceMock = $this->getMockBuilder(ProductViewLogResource::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isExist',
                    'save'
                ]
            )->getMock();
        $logResourceMock->expects($this->once())
            ->method('isExist')
            ->with($productId, $visitorData['visitor_id'])
            ->willReturn(false);
        $this->productViewLogResourceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($logResourceMock);

        $productViewLogMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setProductId',
                    'setVisitorId',
                    'setStoreId',
                    'setCustomerId',
                    'setCustomerGroupId',
                ]
            )->getMock();
        $productViewLogMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setVisitorId')
            ->with($visitorData['visitor_id'])
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $this->productViewLogFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productViewLogMock);

        $visitorMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->willReturn($visitorData['customer_id']);

        $customerMock = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getId',
                    'getGroupId',
                ]
            )->getMockForAbstractClass();
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($visitorData['customer_id']);
        $customerMock->expects($this->once())
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($visitorData['customer_id'])
            ->willReturn($customerMock);

        $productViewLogMock->expects($this->once())
            ->method('setCustomerId')
            ->with($visitorData['customer_id'])
            ->willReturnSelf();
        $productViewLogMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->willReturnSelf();

        $logResourceMock->expects($this->once())
            ->method('save')
            ->with($productViewLogMock)
            ->willThrowException(new \Exception('Error'));

        $this->controller->execute();
    }
}

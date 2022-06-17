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









































































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Model\Source\ProductAttributes;

use Aheadworks\AdvancedReports\Model\Source\ManufacturerAttribute;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeSearchResultsInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Source\ManufacturerAttribute
 */
class ManufacturerAttributeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ManufacturerAttribute
     */
    private $model;

    /**
     * @var ProductAttributeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productAttributeRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var SortOrderBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sortOrderBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->productAttributeRepositoryMock = $this->getMockForAbstractClass(
            ProductAttributeRepositoryInterface::class
        );
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['create', 'addFilter', 'addSortOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock = $this->getMockBuilder(SortOrderBuilder::class)
            ->setMethods(['create', 'setField', 'setAscendingDirection'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            ManufacturerAttribute::class,
            [
                'productAttributeRepository' => $this->productAttributeRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'sortOrderBuilder' => $this->sortOrderBuilderMock
            ]
        );
    }

    /**
     * Testing of toOptionArray method
     * @dataProvider toOptionArrayDataProvider
     */
    public function testToOptionArray($code, $label, $global, $userDefined, $result)
    {
        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->with(ProductAttributeInterface::FRONTEND_LABEL)
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setAscendingDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(ProductAttributeInterface::FRONTEND_INPUT, 'select')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->with($sortOrderMock)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->setMethods(['getIsGlobal', 'getAttributeCode', 'getFrontendLabel'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $attributeMock->expects($this->once())
            ->method('getIsGlobal')
            ->willReturn($global);
        $attributeMock->expects($this->any())
            ->method('getIsUserDefined')
            ->willReturn($userDefined);
        if ($global && $userDefined) {
            $attributeMock->expects($this->once())
                ->method('getAttributeCode')
                ->willReturn($code);
            $attributeMock->expects($this->once())
                ->method('getFrontendLabel')
                ->willReturn($label);
        }

        $productAttributeSearchResultsMock = $this->getMockForAbstractClass(
            ProductAttributeSearchResultsInterface::class
        );
        $productAttributeSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$attributeMock]);

        $this->productAttributeRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($productAttributeSearchResultsMock);

        $this->assertEquals($result, $this->model->toOptionArray());
    }

    /**
     * Data provider for testToOptionArray method
     *
     * @return array
     */
    public function toOptionArrayDataProvider()
    {
        return [
            ['test', 'Test', true, true, [['value' => 'test', 'label' => 'Test']]],
            ['test', 'Test', true, false, []],
            ['test', 'Test', false, true, []],
            ['test', 'Test', false, false, []],
        ];
    }

    /**
     * Testing of getOptions method
     * @dataProvider getOptionsDataProvider
     */
    public function testGetOptions($code, $label, $global, $userDefined, $result)
    {
        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->with(ProductAttributeInterface::FRONTEND_LABEL)
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setAscendingDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(ProductAttributeInterface::FRONTEND_INPUT, 'select')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->with($sortOrderMock)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->setMethods(['getIsGlobal', 'getAttributeCode', 'getFrontendLabel'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $attributeMock->expects($this->once())
            ->method('getIsGlobal')
            ->willReturn($global);
        $attributeMock->expects($this->any())
            ->method('getIsUserDefined')
            ->willReturn($userDefined);
        if ($global && $userDefined) {
            $attributeMock->expects($this->once())
                ->method('getAttributeCode')
                ->willReturn($code);
            $attributeMock->expects($this->once())
                ->method('getFrontendLabel')
                ->willReturn($label);
        }

        $productAttributeSearchResultsMock = $this->getMockForAbstractClass(
            ProductAttributeSearchResultsInterface::class
        );
        $productAttributeSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$attributeMock]);

        $this->productAttributeRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($productAttributeSearchResultsMock);

        $this->assertEquals($result, $this->model->getOptions());
    }

    /**
     * Data provider for getOptions method
     *
     * @return array
     */
    public function getOptionsDataProvider()
    {
        return [
            ['test', 'Test', true, true, ['test'=> 'Test']],
            ['test', 'Test', true, false, []],
            ['test', 'Test', false, true, []],
            ['test', 'Test', false, false, []]
        ];
    }

    /**
     * Testing of getOptionByValue method
     * @dataProvider getOptionByValueDataProvider
     */
    public function testGetOptionByValue($code, $label, $global, $userDefined, $result)
    {
        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->with(ProductAttributeInterface::FRONTEND_LABEL)
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setAscendingDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);
        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(ProductAttributeInterface::FRONTEND_INPUT, 'select')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->with($sortOrderMock)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->setMethods(['getIsGlobal', 'getAttributeCode', 'getFrontendLabel'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $attributeMock->expects($this->once())
            ->method('getIsGlobal')
            ->willReturn($global);
        $attributeMock->expects($this->any())
            ->method('getIsUserDefined')
            ->willReturn($userDefined);
        if ($global && $userDefined) {
            $attributeMock->expects($this->once())
                ->method('getAttributeCode')
                ->willReturn($code);
            $attributeMock->expects($this->once())
                ->method('getFrontendLabel')
                ->willReturn($label);
        }

        $productAttributeSearchResultsMock = $this->getMockForAbstractClass(
            ProductAttributeSearchResultsInterface::class
        );
        $productAttributeSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$attributeMock]);

        $this->productAttributeRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn($productAttributeSearchResultsMock);

        $this->assertEquals($result, $this->model->getOptionByValue($code));
    }

    /**
     * Data provider for getOptionByValue method
     *
     * @return array
     */
    public function getOptionByValueDataProvider()
    {
        return [
            ['test', 'Test', true, true, 'Test'],
            ['test', 'Test', true, false, null],
            ['test', 'Test', false, true, null],
            ['test', 'Test', false, false, null]
        ];
    }
}

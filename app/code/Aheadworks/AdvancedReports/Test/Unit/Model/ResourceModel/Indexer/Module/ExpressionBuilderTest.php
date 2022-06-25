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






































































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Model\ResourceModel\Indexer\Module;

use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module\ExpressionBuilder;
use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module\ExpressionInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module\ExpressionInterfaceFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Test for \Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module\ExpressionBuilder
 */
class ExpressionBuilderTest extends TestCase
{
    /**
     * @var ExpressionBuilder
     */
    private $model;

    /**
     * @var ExpressionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $expressionFactoryMock;

    /**
     * @var ModuleManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->expressionFactoryMock = $this->createPartialMock(
            ExpressionInterfaceFactory::class,
            ['create']
        );

        $this->moduleManagerMock = $this->createPartialMock(
            ModuleManager::class,
            ['isEnabled']
        );

        $this->model = $objectManager->getObject(
            ExpressionBuilder::class,
            [
                'expressionFactory' => $this->expressionFactoryMock,
                'moduleManager' => $this->moduleManagerMock,
            ]
        );
    }

    /**
     * Test addExpression method
     */
    public function testAddExpression()
    {
        $moduleName = 'Vendor_Name';
        $expression = 'expression';

        $expressionMock = $this->getExpressionMock($moduleName, $expression);

        $this->expressionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($expressionMock);

        $this->assertSame($this->model, $this->model->addExpression($moduleName, $expression));
        $this->assertEquals([$expressionMock], $this->getProperty('expressions'));
    }

    /**
     * Test setGroupExpression method
     */
    public function testSetGroupExpression()
    {
        $expression = 'SUM';

        $this->assertSame($this->model, $this->model->setGroupExpression($expression));
        $this->assertEquals($expression, $this->getProperty('groupExpression'));
    }

    /**
     * Test setDefaultEmptyExpression method
     */
    public function testSetDefaultEmptyExpression()
    {
        $expression = '0.0';

        $this->assertSame($this->model, $this->model->setDefaultEmptyExpression($expression));
        $this->assertEquals($expression, $this->getProperty('defaultEmptyExpression'));
    }

    /**
     * Test reset method
     */
    public function testReset()
    {
        $expressionMock = $this->getMockForAbstractClass(ExpressionInterface::class);
        $groupExpression = 'SUM';
        $defaultEmptyExpression = '0.0';

        $this->setProperty('expressions', [$expressionMock]);
        $this->setProperty('groupExpression', $groupExpression);
        $this->setProperty('defaultEmptyExpression', $defaultEmptyExpression);

        $this->assertEquals([$expressionMock], $this->getProperty('expressions'));
        $this->assertEquals($groupExpression, $this->getProperty('groupExpression'));
        $this->assertEquals($defaultEmptyExpression, $this->getProperty('defaultEmptyExpression'));

        $this->model->reset();

        $this->assertEquals([], $this->getProperty('expressions'));
        $this->assertEquals(null, $this->getProperty('groupExpression'));
        $this->assertEquals(null, $this->getProperty('defaultEmptyExpression'));
    }

    /**
     * Test create method
     */
    public function testCreate()
    {
        $moduleOneName = 'Vendor_Name';
        $expressionOne = 'expression1';
        $moduleTwoName = 'Vendor_Name';
        $expressionTwo = 'expression2';
        $groupExpression = 'SUM';
        $defaultEmptyExpression = '0.0';

        $expressionOneMock = $this->getExpressionMock($moduleOneName, $expressionOne);
        $expressionTwoMock = $this->getExpressionMock($moduleTwoName, $expressionTwo);

        $this->setProperty('expressions', [$expressionOneMock, $expressionTwoMock]);
        $this->setProperty('groupExpression', $groupExpression);
        $this->setProperty('defaultEmptyExpression', $defaultEmptyExpression);

        $this->moduleManagerMock->expects($this->exactly(2))
            ->method('isEnabled')
            ->withConsecutive([$moduleOneName], [$moduleTwoName])
            ->willReturnOnConsecutiveCalls(true, false);

        $this->assertEquals($groupExpression . '(' . $expressionOne . ')', $this->model->create());
    }

    /**
     * Test create method if all modules enabled
     */
    public function testCreateAllEnabled()
    {
        $moduleOneName = 'Vendor_Name';
        $expressionOne = 'expression1';
        $moduleTwoName = 'Vendor_Name';
        $expressionTwo = 'expression2';
        $groupExpression = 'SUM';
        $defaultEmptyExpression = '0.0';

        $expressionOneMock = $this->getExpressionMock($moduleOneName, $expressionOne);
        $expressionTwoMock = $this->getExpressionMock($moduleTwoName, $expressionTwo);

        $this->setProperty('expressions', [$expressionOneMock, $expressionTwoMock]);
        $this->setProperty('groupExpression', $groupExpression);
        $this->setProperty('defaultEmptyExpression', $defaultEmptyExpression);

        $this->moduleManagerMock->expects($this->exactly(2))
            ->method('isEnabled')
            ->withConsecutive([$moduleOneName], [$moduleTwoName])
            ->willReturnOnConsecutiveCalls(true, true);

        $this->assertEquals(
            $groupExpression . '(' . $expressionOne . ' + ' . $expressionTwo .')',
            $this->model->create()
        );
    }

    /**
     * Test create method if all modules disabled
     */
    public function testCreateAllDisabled()
    {
        $moduleOneName = 'Vendor_Name';
        $expressionOne = 'expression1';
        $moduleTwoName = 'Vendor_Name';
        $expressionTwo = 'expression2';
        $groupExpression = 'SUM';
        $defaultEmptyExpression = '0.0';

        $expressionOneMock = $this->getExpressionMock($moduleOneName, $expressionOne);
        $expressionTwoMock = $this->getExpressionMock($moduleTwoName, $expressionTwo);

        $this->setProperty('expressions', [$expressionOneMock, $expressionTwoMock]);
        $this->setProperty('groupExpression', $groupExpression);
        $this->setProperty('defaultEmptyExpression', $defaultEmptyExpression);

        $this->moduleManagerMock->expects($this->exactly(2))
            ->method('isEnabled')
            ->withConsecutive([$moduleOneName], [$moduleTwoName])
            ->willReturnOnConsecutiveCalls(false, false);

        $this->assertEquals($groupExpression . '(' . $defaultEmptyExpression .')', $this->model->create());
    }

    /**
     * Test create method if no group expression
     */
    public function testCreateNoGroupExpression()
    {
        $moduleOneName = 'Vendor_Name';
        $expressionOne = 'expression1';
        $moduleTwoName = 'Vendor_Name';
        $expressionTwo = 'expression2';
        $defaultEmptyExpression = '0.0';

        $expressionOneMock = $this->getExpressionMock($moduleOneName, $expressionOne);
        $expressionTwoMock = $this->getExpressionMock($moduleTwoName, $expressionTwo);

        $this->setProperty('expressions', [$expressionOneMock, $expressionTwoMock]);
        $this->setProperty('defaultEmptyExpression', $defaultEmptyExpression);

        $this->moduleManagerMock->expects($this->exactly(2))
            ->method('isEnabled')
            ->withConsecutive([$moduleOneName], [$moduleTwoName])
            ->willReturnOnConsecutiveCalls(true, true);

        $this->assertEquals('(' . $expressionOne . ' + '. $expressionTwo . ')', $this->model->create());
    }

    /**
     * Get expression mock
     *
     * @param string $moduleName
     * @param string $expression
     * @return ExpressionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getExpressionMock($moduleName, $expression)
    {
        $expressionMock = $this->getMockForAbstractClass(ExpressionInterface::class);
        $expressionMock->expects($this->any())
            ->method('getModuleName')
            ->willReturn($moduleName);
        $expressionMock->expects($this->any())
            ->method('getValue')
            ->willReturn($expression);
        $expressionMock->expects($this->any())
            ->method('setModuleName')
            ->with($moduleName)
            ->willReturnSelf();
        $expressionMock->expects($this->any())
            ->method('setValue')
            ->with($expression)
            ->willReturnSelf();

        return $expressionMock;
    }

    /**
     * Get property
     *
     * @param string $name
     * @return mixed
     * @throws \ReflectionException
     */
    private function getProperty($name)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        $value = $property->getValue($this->model);

        return $value;
    }

    /**
     * Set property
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($name, $value)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($this->model, $value);

        return $this;
    }
}

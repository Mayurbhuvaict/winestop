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










































































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Model\Source;

use Aheadworks\AdvancedReports\Model\Source\OrderStatus;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Config;

/**
 * Test for \Aheadworks\AdvancedReports\Model\Source\OrderStatus
 */
class OrderStatusTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderStatus
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->orderConfigMock = $this->getMockBuilder(Config::class)
            ->setMethods(['getStatuses'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            OrderStatus::class,
            ['orderConfig' => $this->orderConfigMock]
        );
    }

    /**
     * Testing of toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->orderConfigMock->expects($this->once())
            ->method('getStatuses')
            ->willReturn([['code' => 'code', 'label' => 'label']]);
        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}

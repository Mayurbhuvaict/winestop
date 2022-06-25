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














































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\View;

use Aheadworks\AdvancedReports\Block\Adminhtml\View\IndexInfo;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReports\Model\Flag;
use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as StatisticsProcessor;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\View\IndexInfo
 */
class IndexInfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var IndexInfo
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Context
     */
    private $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StatisticsProcessor
     */
    private $statisticsProcessorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Flag
     */
    private $flagMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TimezoneInterface
     */
    private $localeDateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AuthorizationInterface
     */
    private $authorizationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UrlInterface
     */
    private $urlBuilderMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getLocaleDate',
                    'getAuthorization',
                    'getUrlBuilder',
                ]
            )
            ->getMock();

        $this->localeDateMock = $this->getMockBuilder(TimezoneInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'formatDate',
                ]
            )
            ->getMockForAbstractClass();
        $this->contextMock->expects($this->any())
            ->method('getLocaleDate')
            ->willReturn($this->localeDateMock);

        $this->authorizationMock = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'isAllowed',
                ]
            )
            ->getMockForAbstractClass();
        $this->contextMock->expects($this->any())
            ->method('getAuthorization')
            ->willReturn($this->authorizationMock);

        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getUrl',
                ]
            )
            ->getMockForAbstractClass();
        $this->contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->flagMock = $this->getMockBuilder(Flag::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'setReportFlagCode',
                'loadSelf',
                'hasData',
                'getLastUpdate'
            ])
            ->getMock();

        $this->statisticsProcessorMock = $this->getMockBuilder(StatisticsProcessor::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'isReindexScheduled'
            ])
            ->getMock();

        $data = [
            'context' => $this->contextMock,
            'flag' => $this->flagMock,
            'statisticsProcessor' => $this->statisticsProcessorMock,
            'data' => [],
        ];
        $this->object = $objectManager->getObject(IndexInfo::class, $data);
    }

    /**
     * Test showLastIndexUpdate method when index was never updated
     */
    public function testShowLastIndexUpdateIndexNeverUpdated()
    {
        $updatedAt = 'undefined';
        $hasLastUpdateData = false;
        $expected = __('The latest Advanced Reports index was updated on %1.', $updatedAt);
        $this->flagMock->expects($this->once())
            ->method('setReportFlagCode')
            ->with(Flag::AW_AREP_STATISTICS_FLAG_CODE)
            ->willReturnSelf();

        $this->flagMock->expects($this->once())
            ->method('loadSelf')
            ->willReturnSelf();

        $this->flagMock->expects($this->once())
            ->method('hasData')
            ->willReturn($hasLastUpdateData);

        $this->assertEquals($expected, $this->object->showLastIndexUpdate());
    }

    /**
     * Test showLastIndexUpdate method when index was updated
     */
    public function testShowLastIndexUpdateIndexUpdated()
    {
        $hasLastUpdateData = true;
        $lastUpdate = '2018-01-15 12:15:58';
        $formattedLastUpdate = 'Jan 15, 2018, 12:15:58 PM';
        $expected = __('The latest Advanced Reports index was updated on %1.', $formattedLastUpdate);
        $this->flagMock->expects($this->once())
            ->method('setReportFlagCode')
            ->with(Flag::AW_AREP_STATISTICS_FLAG_CODE)
            ->willReturnSelf();

        $this->flagMock->expects($this->once())
            ->method('loadSelf')
            ->willReturnSelf();

        $this->flagMock->expects($this->once())
            ->method('hasData')
            ->willReturn($hasLastUpdateData);

        $this->flagMock->expects($this->once())
            ->method('getLastUpdate')
            ->willReturn($lastUpdate);

        $this->localeDateMock->expects($this->once())
            ->method('formatDate')
            ->with($lastUpdate, \IntlDateFormatter::MEDIUM, true)
            ->willReturn($formattedLastUpdate);

        $this->assertEquals($expected, $this->object->showLastIndexUpdate());
    }

    /**
     * Test canShowScheduleMessage method
     *
     * @dataProvider dataProviderTestCanShowScheduleMessage
     * @param bool $isAuthorizationResourceAllowed
     * @param bool $isReindexScheduled
     * @param bool $expected
     */
    public function testCanShowScheduleMessage($isAuthorizationResourceAllowed, $isReindexScheduled, $expected)
    {
        $authorizationResource = 'Aheadworks_AdvancedReports::reports_statistics';
        $this->authorizationMock->expects($this->once())
            ->method('isAllowed')
            ->with($authorizationResource)
            ->willReturn($isAuthorizationResourceAllowed);

        $this->statisticsProcessorMock->expects($this->atMost(1))
            ->method('isReindexScheduled')
            ->willReturn($isReindexScheduled);

        $this->assertEquals($expected, $this->object->canShowScheduleMessage());
    }

    /**
     * Retrieve data provider for testCanShowScheduleMessage test
     *
     * @return array
     */
    public function dataProviderTestCanShowScheduleMessage()
    {
        return [
            [true, false, true],
            [true, true, false],
            [false, false, false],
            [false, true, false],
        ];
    }

    /**
     * Test getIndexUpdateUrl method
     */
    public function testGetIndexUpdateUrl()
    {
        $baseWebUrl = 'https://ecommerce.aheadworks.com/';
        $route = 'advancedreports/statistics/schedule';
        $expected = $baseWebUrl . $route;

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($route)
            ->willReturn($baseWebUrl . $route);

        $this->assertEquals($expected, $this->object->getIndexUpdateUrl());
    }
}

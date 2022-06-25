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













































































































































































































































































































namespace Aheadworks\AdvancedReports\Test\Unit\Block\Adminhtml\Form\Field;

use Aheadworks\AdvancedReports\Block\Adminhtml\Form\Field\Range;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\AdvancedReports\Block\Adminhtml\Form\Field\Range
 */
class RangeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Range
     */
    private $object;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(Range::class);
    }

    /**
     * Test _prepareToRender method
     */
    public function testPrepareToRender()
    {
        $class = new \ReflectionClass(Range::class);
        $method = $class->getMethod('_prepareToRender');
        $method->setAccessible(true);

        $method->invoke($this->object);

        $this->assertEquals(2, count($this->object->getColumns()));
        $this->assertEquals('range', $this->object->getHtmlId());
    }
}

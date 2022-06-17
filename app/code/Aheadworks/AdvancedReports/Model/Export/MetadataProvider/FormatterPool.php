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














































































































































































namespace Aheadworks\AdvancedReports\Model\Export\MetadataProvider;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter\FormatterInterface;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter\Text;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter\Price;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter\Option;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter\Percent;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter\Date;
use Aheadworks\AdvancedReports\Model\Export\MetadataProvider\Formatter\Number;

/**
 * Class FormatterPool
 *
 * @package Aheadworks\AdvancedReports\Model\Export\MetadataProvider
 */
class FormatterPool
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $formatterList = [
        Text::TYPE => Text::class,
        Price::TYPE => Price::class,
        Option::TYPE => Option::class,
        Percent::TYPE => Percent::class,
        Date::TYPE => Date::class,
        Number::TYPE => Number::class
    ];

    /**
     * @var FormatterInterface[]
     */
    private $formatterInstances = [];

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $formatterList
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $formatterList = []
    ) {
        $this->objectManager = $objectManager;
        $this->formatterList = array_merge($this->formatterList, $formatterList);
    }

    /**
     * Retrieve formatter by export data type
     *
     * @param string $dataType
     * @return FormatterInterface
     * @throws \InvalidArgumentException
     */
    public function getFormatter($dataType)
    {
        if (!isset($this->formatterInstances[$dataType])) {
            if (!isset($this->formatterList[$dataType])) {
                $dataType = Text::TYPE;
            }

            $formatterInstance = $this->objectManager->create($this->formatterList[$dataType]);
            if (!$formatterInstance instanceof FormatterInterface) {
                throw new \InvalidArgumentException(
                    sprintf('Formatter instance %s does not implement required interface.', $dataType)
                );
            }
            $this->formatterInstances[$dataType] = $formatterInstance;
        }
        return $this->formatterInstances[$dataType];
    }
}

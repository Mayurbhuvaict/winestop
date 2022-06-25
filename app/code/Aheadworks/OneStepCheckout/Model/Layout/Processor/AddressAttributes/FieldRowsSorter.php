<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes;

use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultSortOrder;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\Configurator;

/**
 * Class FieldRowsSorter
 *
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes
 */
class FieldRowsSorter
{
    /**
     * @var DefaultSortOrder
     */
    private $defaultSortOrder;

    /**
     * @var Configurator
     */
    private $configurator;

    /**
     * @param DefaultSortOrder $defaultSortOrder
     * @param Configurator $configurator
     */
    public function __construct(
        DefaultSortOrder $defaultSortOrder,
        Configurator $configurator
    ) {
        $this->defaultSortOrder = $defaultSortOrder;
        $this->configurator = $configurator;
    }

    /**
     * Sort field rows
     *
     * @param array $config
     * @param string $addressType
     * @return array
     */
    public function sort(array $config, $addressType)
    {
        $configurator = $this->configurator->getCustomizationConfig($addressType);
        foreach ($config as $rowId => &$rowConfig) {
            $rowConfig['sortOrder'] =  $configurator['sort_orders'][$rowId]
                ?? $this->defaultSortOrder->getRowSortOrder($rowId);

            foreach ($rowConfig['children'] as $fieldName => &$child) {
                $child['sortOrder'] = $configurator['fields'][$fieldName]['sort_order']
                    ?? $this->defaultSortOrder->getFieldSortOrder();
            }
            $this->defaultSortOrder->resetFieldSortOrder();
        }

        return $config;
    }
}

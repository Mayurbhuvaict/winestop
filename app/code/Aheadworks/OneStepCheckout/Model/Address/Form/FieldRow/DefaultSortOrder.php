<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow;

/**
 * Class DefaultSortOrder
 *
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow
 */
class DefaultSortOrder
{
    /**
     * @var array
     */
    private $defaultFieldRowsSortOrder = [
        'name-field-row' => 0,
        'address-field-row' => 1,
        'city-field-row' => 2,
        'included-country-field-row' => 3,
        'phone-company-field-row' => 4
    ];

    /**
     * @var int
     */
    private $rowSortOrder = 15;

    /**
     * @var int
     */
    private $fieldSortOrder = 0;

    /**
     * Get default row sort order
     *
     * @param string $rowId
     * @return int
     */
    public function getRowSortOrder($rowId)
    {
        if (isset($this->defaultFieldRowsSortOrder[$rowId])) {
            $sortOrder = $this->defaultFieldRowsSortOrder[$rowId];
        } else {
            $sortOrder = $this->rowSortOrder;
            $this->rowSortOrder ++;
        }

        return $sortOrder;
    }

    /**
     * Get default field sort order
     *
     * @return int
     */
    public function getFieldSortOrder()
    {
        $currentSortOrder = $this->fieldSortOrder;
        $this->fieldSortOrder ++;

        return $currentSortOrder;
    }

    /**
     * Reset field sort order
     */
    public function resetFieldSortOrder()
    {
        $this->fieldSortOrder = 0;
    }
}

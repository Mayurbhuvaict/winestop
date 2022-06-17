<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Import
 * @package Mageplaza\GiftCard\Model
 */
class Import extends AbstractModel
{
    /**
     * Valid column names
     *
     * @var array
     */
    protected $_columnNames = ['code'];

    /**
     * @param array $rawData
     *
     * @return array
     * @throws InputException
     */
    public function processDataBunch($rawData)
    {
        $bunch      = [];
        $colHeaders = $this->getColHeaders($rawData);

        if ($absentColumns = array_diff($this->_columnNames, $colHeaders)) {
            throw new InputException(__('Column <b>%1</b> not found', implode(', ', $absentColumns)));
        }

        /** @var array $rowData */
        foreach ($rawData as $rowIndex => $rowData) {
            if ($rowIndex === 0) {
                continue;
            }

            $temp = [];
            foreach ($rowData as $key => $value) {
                $temp[$colHeaders[$key]] = $value;
            }
            $bunch[] = $temp;
        }

        if (empty($bunch)) {
            throw new InputException(__('Invalid entity'));
        }

        return $bunch;
    }

    /**
     * @param array $rawData
     *
     * @return array
     */
    private function getColHeaders($rawData)
    {
        $colHeaders = [];

        /** @var array $rowData */
        foreach ($rawData as $rowIndex => $rowData) {
            if ($rowIndex === 0) {
                foreach ($rowData as $rowHeader) {
                    $colHeaders[] = $rowHeader;
                }

                break;
            }
        }

        return $colHeaders;
    }
}

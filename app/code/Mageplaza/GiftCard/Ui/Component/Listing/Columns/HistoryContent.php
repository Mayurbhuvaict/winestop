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

namespace Mageplaza\GiftCard\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCard\Action;

/**
 * Class HistoryContent
 * @package Mageplaza\GiftCard\Ui\Component\Listing\Columns
 */
class HistoryContent extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $params = is_array($item['extra_content'])
                    ? $item['extra_content'] : Data::jsonDecode($item['extra_content']);

                $item[$this->getData('name')] = Action::getActionLabel($item['action'], $params);
            }
        }

        return $dataSource;
    }
}

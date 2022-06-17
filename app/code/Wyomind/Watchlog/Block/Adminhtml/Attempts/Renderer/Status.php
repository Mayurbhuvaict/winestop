<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Block\Adminhtml\Attempts\Renderer;

/**
 * Class Status
 * @package Wyomind\Watchlog\Block\Adminhtml\Attempts\Renderer
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $type = "notice";
        $inner = "SUCCESS";
        switch ($row->getStatus()) {
            case \Wyomind\Watchlog\Helper\Data::FAILURE;
                $type = 'major';
                $inner = "FAILURE";
                break;
        }
        return "<span class='grid-severity-" . $type . "'>" . $inner . "</span>";
    }
}

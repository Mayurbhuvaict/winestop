<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Block\Adminhtml\Attempts\Renderer;

/**
 * Class Ip
 * @package Wyomind\Watchlog\Block\Adminhtml\Attempts\Renderer
 */
class Ip extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return "<a target='_blank' href='http://www.abuseipdb.com/check/".$row->getIp()."' title='".__('Check this ip')."'>".$row->getIp()."</a>";
    }
}

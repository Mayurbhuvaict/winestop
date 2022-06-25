<?php
namespace Winestop\Custom\Plugin\Payment\Block;
 
class Info
{
    public function beforeToHtml(\Magento\Payment\Block\Info $subject)
    {        
        $subject->setTemplate('Winestop_Custom::info/default.phtml');        
    }
}
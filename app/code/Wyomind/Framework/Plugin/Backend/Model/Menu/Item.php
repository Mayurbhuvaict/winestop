<?php

namespace Wyomind\Framework\Plugin\Backend\Model\Menu;

use Wyomind\Framework\Helper\Module;

/**
 * Class Item
 * @package Wyomind\Framework\Plugin\Backend\Model\Menu
 */
class Item
{

    /**
     * @var Module
     */
    protected $module;

    /**
     * Item constructor.
     * @param Module $module
     */
    public function __construct(
        Module $module
    ) {
    
        $this->module = $module;
    }
//
//    public function afterGetTitle($subject, $title) {
//        return $title.' ['.$subject->toArray()['sort_index'].']';
//    }

    public function afterGetUrl($subject, $url)
    {
        $id = $subject->getId();
        $allExtensions = $this->module->getAllExtensions();

        if (strpos($id, '::userguide') !== false
            && strpos($id, 'Wyomind') !== false) {
            $module = explode('::', $id);
            $module = $module[0];
            $prefix = $this->module->getPrefix($module);
            $namespace = explode("_", $module);
            $namespace = $namespace[1];
            $moduleCode = $this->module->getDefaultConfig(strtolower($prefix . $namespace) . "/license/extension_code");
            if (isset($allExtensions['by_code'][strtoupper($moduleCode)]['user-guide'])) {
                $url = $allExtensions['by_code'][strtoupper($moduleCode)]['user-guide'] . "?section=userguide";
            } else {
                $url = "https://www.wyomind.com/customer/support/index";
                if (Builder::MARKETPLACE) {
                    $url = "https://marketplace.magento.com/partner/Wyomind";
                }
            }
        } elseif (strpos($id, '::support') !== false
            && strpos($id, 'Wyomind') !== false) {
            $url = "https://www.wyomind.com/customer/support/index";
            if (Builder::MARKETPLACE) {
                $url = "https://marketplace.magento.com/partner/Wyomind";
            }
        } elseif (strpos($id, '::discover_') !== false && strpos($id, '::discover_group') === false
            && strpos($id, 'Wyomind') !== false) {
            $module = str_replace('Wyomind_Framework::discover_', '', $id);
            if (Builder::MARKETPLACE && isset($allExtensions['by_code'][strtoupper($module)]['namespace'])) {
                $url = "https://marketplace.magento.com/wyomind-" . $allExtensions['by_code'][strtoupper($module)]['namespace'] . "-meta.html";
            } elseif (isset($allExtensions['by_code'][strtoupper($module)]['user-guide'])) {
                $url = $allExtensions['by_code'][strtoupper($module)]['user-guide'];
            }
        } elseif (strpos($id, '::payg') !== false
            && strpos($id, 'Wyomind') !== false) {
            $url = "https://www.wyomind.com/pay-as-you-go-development.html";
            if (Builder::MARKETPLACE) {
                $url = "https://marketplace.magento.com/partner/Wyomind";
            }
        }
        return $url;

    }
}

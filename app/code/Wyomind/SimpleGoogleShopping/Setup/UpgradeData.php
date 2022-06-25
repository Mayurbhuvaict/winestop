<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Setup;

/**
 * Upgrade data for Simple Google Shopping
 */
class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory|null
     */
    private $_feedsCollection = null;

    /**
     * @var \Magento\Framework\App\State|null
     */
    private $_state = null;

    /**
     * @var \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Functions\CollectionFactory|null
     */
    private $_functionCollectionFactory = null;

    /**
     * UpgradeData constructor.
     * @param \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory
     * @param \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Functions\CollectionFactory $functionCollectionFactory
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory,
        \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Functions\CollectionFactory $functionCollectionFactory,
        \Magento\Framework\App\State $state
    ) {
    
        $this->_feedsCollection = $feedsCollectionFactory;
        $this->_functionCollectionFactory = $functionCollectionFactory;
        $this->_state = $state;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
    
        $setup->startSetup();
        /**
         * upgrade to 11.0.0
         */
        if (version_compare($context->getVersion(), '11.0.0') < 0) {
            try {
                $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } catch (\Exception $e) {
            }
            foreach ($this->_feedsCollection as $feed) {
                $pattern = str_replace(['"{{', '}}"', "'{{", "}}'", "php="], ['{{', '}}', "{{", "}}", "output="], $feed->getSimplegoogleshoppingXmlitempattern());
                $feed->setSimplegoogleshoppingXmlitempattern($pattern);
                $feed->save();
            }
            //.categories index=
            $re = '/.categories([^|}]+)index="?\'?([0-9]+)"?\'?/';
            foreach ($this->_feedsCollection as $feed) {
                $pattern = $feed->getProductPattern();
                $pattern = preg_replace($re, '.categories${1}nth="${2}"', $pattern);
                $feed->setProductPattern($pattern);
                $feed->save();
            }
        }

        /**
         * upgrade to 11.0.2
         * $myPattern = null; becomes $this->skip = true;
         */
        if (version_compare($context->getVersion(), '11.0.1') < 0) {
            try {
                $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } catch (\Exception $e) {
            }
            foreach ($this->_feedsCollection as $feed) {
                $pattern = $feed->getSimplegoogleshoppingXmlitempattern();
                $re = '/\$myPattern\s*=\s*null;/';
                preg_match_all($re, $pattern, $matches);
                foreach ($matches[0] as $match) {
                    $pattern = str_replace($match, '$this->skip();', $pattern);
                }
                $feed->setSimplegoogleshoppingXmlitempattern($pattern);
                $feed->save();
            }
        }

        /**
         * upgrade to 13.1.0.1
         * custom functions => sgs_* to wyomind_*
         */
        if (version_compare($context->getVersion(), '13.1.0') < 0) {
            try {
                $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } catch (\Exception $e) {
            }
            $toReplace = ["sgs_strtoupper", "sgs_strtolower", "sgs_implode", "sgs_html_entity_decode", "sgs_strip_tags", "sgs_htmlentities", "sgs_substr"];
            $replacement = ["wyomind_strtoupper", "wyomind_strtolower", "wyomind_implode", "wyomind_html_entity_decode", "wyomind_strip_tags", "wyomind_htmlentities", "wyomind_substr"];

            $functionCollection = $this->_functionCollectionFactory->create();

            foreach ($functionCollection as $function) {
                $function->setScript(str_replace($toReplace, $replacement, $function->getScript()));
                $function->save();
            }

            foreach ($this->_feedsCollection as $feed) {
                $pattern = $feed->getSimplegoogleshoppingXmlitempattern();
                $pattern = str_replace($toReplace, $replacement, $pattern);
                $feed->setSimplegoogleshoppingXmlitempattern($pattern);
                $feed->save();
            }
        }

        /**
         * upgrade to 13.1.0.1
         * custom functions => sgs_* to wyomind_*
         */
        if (version_compare($context->getVersion(), '13.1.2') < 0) {
            try {
                $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            } catch (\Exception $e) {
            }

            $functionCollection = $this->_functionCollectionFactory->create();

            foreach ($functionCollection as $function) {
                $searchQuery = "<?php if (!function_exists(";
                if (substr($function->getScript(), 0, strlen($searchQuery)) !== $searchQuery) {
                    $function->setScript(preg_replace("/<\?php\sfunction\s([a-zA-z0-9]+)/", '<?php if (!function_exists("\1")) { function \1', $function->getScript()));
                    $function->setScript(str_replace("?>", "}\n?>", $function->getScript()));
                    $function->save();
                }
            }
        }

        $setup->endSetup();
    }
}

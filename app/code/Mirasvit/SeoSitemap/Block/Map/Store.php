<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Block\Map;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\SeoSitemap\Model\Config;

class Store extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * Store constructor.
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        Config $config,
        Context $context
    ) {
        $this->config  = $config;
        $this->context = $context;

        parent::_construct();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __('Stores');
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        return $this->context->getStoreManager()->getStores();
    }

    /**
     * @return bool|int
     */
    public function canShowStores()
    {
        return $this->config->getIsShowStores();
    }
}

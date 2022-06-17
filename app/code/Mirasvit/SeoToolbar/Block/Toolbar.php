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



namespace Mirasvit\SeoToolbar\Block;

use function GuzzleHttp\Psr7\str;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Helper\Data as DataHelper;
use Mirasvit\Seo\Helper\Analyzer;
use Mirasvit\Seo\Api\Config\InfoInterface;
use Mirasvit\SeoToolbar\Api\Data\DataProviderItemInterface;
use Mirasvit\SeoToolbar\Api\Service\DataProviderInterface;
use Mirasvit\SeoToolbar\Model\Config;
use Mirasvit\SeoToolbar\Service\InfoService;

class Toolbar extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mirasvit_SeoToolbar::toolbar.phtml';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DataProviderInterface[]
     */
    private $dataProviderPool;

    /**
     * Toolbar constructor.
     * @param Context $context
     * @param Config $config
     * @param array $dataProviderPool
     */
    public function __construct(
        Context $context,
        Config $config,
        array $dataProviderPool
    ) {
        $this->dataProviderPool = $dataProviderPool;
        $this->config = $config;

        ksort($this->dataProviderPool);

        parent::__construct($context);
    }

    /**
     * @return DataProviderItemInterface[][]
     */
    public function getSections()
    {
        $sections = [];

        foreach ($this->dataProviderPool as $dataProvider) {
            $sections[(string)$dataProvider->getTitle()] = $dataProvider->getItems();
        }

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function _toHtml()
    {
        if (!$this->config->isToolbarAllowed()) {
            return '';
        }

        return parent::_toHtml();
    }
}

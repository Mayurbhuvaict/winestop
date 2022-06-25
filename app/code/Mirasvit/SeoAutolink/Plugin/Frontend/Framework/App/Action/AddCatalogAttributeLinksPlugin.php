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



namespace Mirasvit\SeoAutolink\Plugin\Frontend\Framework\App\Action;

use Magento\Catalog\Helper\Output as CatalogOutputHelper;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoAutolink\Model\Config;
use Mirasvit\SeoAutolink\Model\Config\Source\Target;
use Mirasvit\SeoAutolink\Service\TextProcessorService;

class AddCatalogAttributeLinksPlugin
{
    /**
     * @var CatalogOutputHelper
     */
    private $catalogOutputHelper;

    /**
     * @var StateServiceInterface
     */
    private $stateService;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var TextProcessorService
     */
    private $textProcessorService;

    /**
     * AddCatalogAttributeLinksPlugin constructor.
     * @param CatalogOutputHelper $catalogOutputHelper
     * @param StateServiceInterface $stateService
     * @param Config $config
     * @param TextProcessorService $textProcessorService
     */
    public function __construct(
        CatalogOutputHelper $catalogOutputHelper,
        StateServiceInterface $stateService,
        Config $config,
        TextProcessorService $textProcessorService
    ) {
        $this->catalogOutputHelper  = $catalogOutputHelper;
        $this->stateService         = $stateService;
        $this->config               = $config;
        $this->textProcessorService = $textProcessorService;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param object                                 $response
     *
     * @return object
     */
    public function afterDispatch($subject, $response)
    {
        if ($subject instanceof \Magento\Framework\App\Action\Forward) {
            return $response;
        }

        $this->catalogOutputHelper->addHandler('productAttribute', $this);
        $this->catalogOutputHelper->addHandler('categoryAttribute', $this);

        return $response;
    }

    /**
     * @param CatalogOutputHelper $outputHelper
     * @param string              $outputHtml
     * @param array               $params
     *
     * @return string
     */
    public function productAttribute($outputHelper, $outputHtml, $params)
    {
        if (!$this->stateService->isProductPage()) {
            return $outputHtml;
        }

        switch ($params['attribute']) {
            case 'short_description':
                if ($this->config->isAllowedTarget(Target::PRODUCT_SHORT_DESCRIPTION)) {
                    $outputHtml = $this->textProcessorService->addLinks($outputHtml);
                }

                break;

            case 'description':
                if ($this->config->isAllowedTarget(Target::PRODUCT_FULL_DESCRIPTION)) {
                    $outputHtml = $this->textProcessorService->addLinks($outputHtml);
                }
                break;

            default:
                if ($this->config->isAllowedTarget(Target::PRODUCT_ATTRIBUTE)) {
                    $outputHtml = $this->textProcessorService->addLinks($outputHtml);
                }
        }

        return $outputHtml;
    }

    /**
     * @param CatalogOutputHelper $outputHelper
     * @param string              $outputHtml
     * @param array               $params
     *
     * @return string
     */
    public function categoryAttribute($outputHelper, $outputHtml, $params)
    {
        if (!$this->stateService->isCategoryPage()) {
            return $outputHtml;
        }

        if ($params['attribute'] == 'description' && $this->config->isAllowedTarget(Target::CATEGORY_DESCRIPTION)) {
            $outputHtml = $this->textProcessorService->addLinks($outputHtml);
        }

        return $outputHtml;
    }
}

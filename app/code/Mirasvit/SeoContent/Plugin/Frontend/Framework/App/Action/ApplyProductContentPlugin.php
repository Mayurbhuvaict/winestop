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



namespace Mirasvit\SeoContent\Plugin\Frontend\Framework\App\Action;

use Magento\Catalog\Helper\Output as CatalogOutputHelper;
use Mirasvit\Seo\Api\Service\StateServiceInterface;
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Service\ContentService;

class ApplyProductContentPlugin
{
    private $isShortDescriptionWasProcessed   = false;

    private $isOgShortDescriptionWasProcessed = false;

    private $isDescriptionWasProcessed        = false;

    private $contentService;

    private $stateService;

    private $catalogOutputHelper;

    public function __construct(
        ContentService $contentService,
        StateServiceInterface $stateService,
        CatalogOutputHelper $catalogOutputHelper
    ) {
        $this->contentService      = $contentService;
        $this->stateService        = $stateService;
        $this->catalogOutputHelper = $catalogOutputHelper;
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

        if (!in_array($params['attribute'], ['name', 'short_description', 'og:short_description', 'description'])) {
            return $outputHtml;
        }

        $product = $params['product'];

        if ($product->getId() != $this->stateService->getProduct()->getId()) {
            return $outputHtml;
        }

        $content = $this->contentService->getCurrentContent();

        switch ($params['attribute']) {
            case 'name':
                if ($content->getTitle()) {
                    $outputHtml = $content->getTitle();
                }
                break;
            case 'short_description':
                if ($content->getShortDescription() && !$this->isShortDescriptionWasProcessed) {
                    $this->isShortDescriptionWasProcessed = true; #prevent recursive call

                    $outputHtml = $this->catalogOutputHelper->productAttribute(
                        $product,
                        $content->getShortDescription(),
                        'short_description'
                    );
                } elseif ($content->getDescription()
                    && $content->getDescriptionPosition() == ContentInterface::DESCRIPTION_POSITION_UNDER_SHORT_DESCRIPTION) {
                    $outputHtml .= $content->getDescription();
                }

                break;

            case 'og:short_description':
                if ($content->getShortDescription() && !$this->isOgShortDescriptionWasProcessed) {
                    $this->isOgShortDescriptionWasProcessed = true; #prevent recursive call

                    $outputHtml = $this->catalogOutputHelper->productAttribute(
                        $product,
                        $content->getShortDescription(),
                        'short_description'
                    );
                    $this->isShortDescriptionWasProcessed = false;
                }

                break;

            case 'description':
                if ($content->getFullDescription() && !$this->isDescriptionWasProcessed) {
                    $this->isDescriptionWasProcessed = true; #prevent recursive call

                    $outputHtml = $this->catalogOutputHelper->productAttribute(
                        $product,
                        $content->getFullDescription(),
                        'description'
                    );
                } elseif ($content->getDescription()
                    && $content->getDescriptionPosition() == ContentInterface::DESCRIPTION_POSITION_UNDER_FULL_DESCRIPTION) {
                    $outputHtml .= $content->getDescription();
                }

                break;
        }

        return $outputHtml;
    }
}

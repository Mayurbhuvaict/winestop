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



namespace Mirasvit\SeoContent\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\SeoContent\Api\Data\ContentInterface;
use Mirasvit\SeoContent\Service\ContentService;

class Description extends Template
{
    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var mixed|string|null
     */
    private $position = null;

    /**
     * Description constructor.
     * @param ContentService $contentService
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ContentService $contentService,
        Context $context,
        array $data = []
    ) {
        $this->contentService = $contentService;
        $this->position = isset($data['position']) ? $data['position'] : '';

        parent::__construct($context, $data);
    }

    /**
     * @return bool|string
     */
    public function getDescription()
    {
        $content = $this->contentService->getCurrentContent();

        if (!$content->getDescription()) {
            return false;
        }

        if ($this->position == 'bottom'
            && $content->getDescriptionPosition() == ContentInterface::DESCRIPTION_POSITION_BOTTOM_PAGE) {
            return $content->getDescription();
        }

        if ($this->position == 'content'
            && $content->getDescriptionPosition() == ContentInterface::DESCRIPTION_POSITION_UNDER_PRODUCT_LIST) {
            return $content->getDescription();
        }

        return false;
    }
}

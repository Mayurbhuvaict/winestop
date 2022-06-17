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



namespace Mirasvit\SeoContent\Plugin\Frontend\Framework\View\Page\Title;

use Mirasvit\SeoContent\Service\ContentService;

class ApplyMetaTitlePlugin
{
    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * ApplyMetaTitlePlugin constructor.
     * @param ContentService $contentService
     */
    public function __construct(
        ContentService $contentService
    ) {
        $this->contentService = $contentService;
    }

    /**
     * @param mixed $subject
     * @param mixed $result
     * @return string
     */
    public function afterGet($subject, $result)
    {
        if (!$this->contentService->isProcessablePage()) {
            return $result;
        }

        $title = $this->contentService->getCurrentContent()->getMetaTitle();

        if ($title) {
            return $title;
        }

        return $result;
    }
}

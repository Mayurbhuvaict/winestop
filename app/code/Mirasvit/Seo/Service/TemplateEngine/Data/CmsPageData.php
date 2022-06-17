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


declare(strict_types=1);

namespace Mirasvit\Seo\Service\TemplateEngine\Data;

use Magento\Cms\Model\Page;
use Magento\Framework\Registry;

class CmsPageData extends AbstractData
{
    private $page;

    private $registry;

    public function __construct(
        Page $page,
        Registry $registry
    ) {
        $this->page     = $page;
        $this->registry = $registry;

        parent::__construct();
    }

    public function getTitle(): string
    {
        return (string)__('CMS Page Data');
    }

    public function getVariables(): array
    {
        return [
            'title',
            'meta_keywords',
            'meta_description',
            'content_heading',
            'content',
            'meta_title',
            'apply_for_homepage',
        ];
    }

    public function getValue(string $attribute, array $additionalData = []): ?string
    {
        $page = $this->page;

        if (!$this->page->getId() && $this->registry->registry('current_cms_page')) {
            $page = $this->registry->registry('current_cms_page');
        }

        if (!$page->getIdentifier()) {
            return null;
        }

        return $page->getDataUsingMethod($attribute) ?: null;
    }
}

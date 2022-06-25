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

use Magento\Framework\View\Page\Config as PageConfig;

class PageData extends AbstractData
{
    private $pageConfig;

    public function __construct(
        PageConfig $pageConfig
    ) {
        $this->pageConfig = $pageConfig;

        parent::__construct();
    }

    public function getTitle(): string
    {
        return (string)__('Current Page Data');
    }

    public function getVariables(): array
    {
        return [
            'title',
            'meta_description',
            'meta_keywords',
        ];
    }

    public function getValue(string $attribute, array $additionalData = []): ?string
    {
        switch ($attribute) {
            case 'title':
                return (string)$this->pageConfig->getTitle()->getShortHeading();

            case 'meta_description':
                return (string)$this->pageConfig->getDescription();

            case 'meta_keywords':
                return (string)$this->pageConfig->getKeywords();
        }

        return null;
    }
}

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

namespace Mirasvit\SeoContent\Service\Content\Modifier;

use Mirasvit\SeoContent\Api\Data\ContentInterface;

/**
 * Purpose: Remove non-allowed chars in meta
 */
class CleanupModifier implements ModifierInterface
{
    public function modify(ContentInterface $content, ?string $forceApplyTo = null): ContentInterface
    {
        $metaProperties = [
            ContentInterface::META_TITLE,
            ContentInterface::META_KEYWORDS,
            ContentInterface::META_DESCRIPTION,
        ];

        foreach ($metaProperties as $property) {
            $content->setData($property, $this->cleanupMeta(
                $content->getData($property)
            ));
        }

        return $content;
    }

    private function cleanupMeta(?string $meta): ?string
    {
        if (!$meta) {
            return $meta;
        }
        
        $meta = strip_tags($meta);
        $meta = preg_replace('/\s{2,}/', ' ', $meta); //remove unnecessary spaces
        $meta = preg_replace('/\"/', ' ', $meta); //remove " because it destroys html
        $meta = trim($meta);

        return $meta;
    }
}

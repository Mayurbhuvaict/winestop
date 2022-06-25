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


namespace Mirasvit\SeoAudit\Parser;


use Mirasvit\SeoAudit\Api\Data\UrlInterface;

class RobotsParser extends AbstractParser
{
    public function retriveUrls(UrlInterface $url, int $jobId): void
    {
        if (!$url->getContent()) {
            return;
        }

        preg_match_all('#Sitemap:\s*(https?://[\S]*)\s*$#is', $url->getContent(), $matches);

        foreach ($matches as $m) {
            if (isset($m[1])) {
                $this->urlService->ensureUrl($m[1], UrlInterface::TYPE_SITEMAP, $jobId, $url->getId());
            }
        }
    }
}

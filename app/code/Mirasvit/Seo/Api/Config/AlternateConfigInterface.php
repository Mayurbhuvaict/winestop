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

namespace Mirasvit\Seo\Api\Config;

interface AlternateConfigInterface
{
    const ALTERNATE_DEFAULT       = 1;
    const ALTERNATE_CONFIGURABLE  = 2;
    const X_DEFAULT_AUTOMATICALLY = 'AUTOMATICALLY';
    const AMASTY_XLANDING         = 'amasty_xlanding_page_view'; //amasty_xlanding page

    public function getAlternateHreflang(int $storeId): int;

    /**
     * @return array|string
     */
    public function getAlternateManualConfig(int $storeId, bool $hreflang = false);

    public function getAlternateManualXDefault(array $storeUrls): ?string;

    public function isHreflangLocaleCodeAddAutomatical(): bool;

    public function isHreflangCutCategoryAdditionalData(): bool;

    public function getXDefault(): string;

    public function getHreflangLocaleCode(int $storeId): string;
}

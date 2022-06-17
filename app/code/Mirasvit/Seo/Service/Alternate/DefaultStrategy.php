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

namespace Mirasvit\Seo\Service\Alternate;

use Mirasvit\Seo\Api\Service\Alternate\UrlInterface;

class DefaultStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    protected $url;

    public function __construct(
        UrlInterface $url
    ) {
        $this->url = $url;
    }

    public function getStoreUrls(): array
    {
        $storeUrls = $this->url->getStoresCurrentUrl();

        return $storeUrls;
    }

    public function getAlternateUrl(array $storeUrls): array
    {
        return [];
    }
}

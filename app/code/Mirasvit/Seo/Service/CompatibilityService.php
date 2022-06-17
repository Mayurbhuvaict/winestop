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



namespace Mirasvit\Seo\Service;

use Mirasvit\Seo\Api\Service\CompatibilityServiceInterface;
use Mirasvit\Seo\Helper\Version;
use Mirasvit\Core\Service\SerializeService;

/**
 * M2.2. compatibility
 */
class CompatibilityService implements CompatibilityServiceInterface
{
    /**
     * @var Version
     */
    private $version;

    /**
     * @param Version $version
     */
    public function __construct(
        Version $version
    ) {
        $this->version = $version;
    }

    /**
     * Prepare Rule Data For Save ('conditions_serialized', 'actions_serialized')
     *
     * {@inheritdoc}
     */
    public function prepareRuleDataForSave($value)
    {
        if ($this->version->getVersion() >= '2.2.0'
            && !json_decode($value)) {
            $srcValue = $value;
            $value = SerializeService::decode($value);
            if (!$value) {
                $value = [0 => $srcValue];
            }
            $value = SerializeService::encode($value);
        }

        return $value;
    }
}

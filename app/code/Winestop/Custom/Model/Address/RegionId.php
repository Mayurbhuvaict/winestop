<?php
namespace Winestop\Custom\Model\Address;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Region as RegionSource;

/**
 * Class RegionId
 */
class RegionId extends \Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\RegionId
{
    /**
     * {@inheritdoc}
     */
    public function modify($metadata, $addressType)
    {
        return $metadata;
    }
}

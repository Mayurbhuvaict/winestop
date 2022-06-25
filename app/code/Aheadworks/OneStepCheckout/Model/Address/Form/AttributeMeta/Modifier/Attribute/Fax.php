<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;

/**
 * Class Fax
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute
 */
class Fax implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify($metadata, $addressType)
    {
        $metadata['system'] = true;
        return $metadata;
    }
}

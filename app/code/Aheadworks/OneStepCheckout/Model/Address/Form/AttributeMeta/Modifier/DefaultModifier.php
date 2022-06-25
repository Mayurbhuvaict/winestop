<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier;

/**
 * Class DefaultModifier
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier
 */
class DefaultModifier implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify($metadata, $addressType)
    {
        return $metadata;
    }
}

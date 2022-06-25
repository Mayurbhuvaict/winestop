<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Model\Api\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mageplaza\GiftCard\Api\Data\TemplateFieldsInterface;

/**
 * Class TemplateFields
 * @package Mageplaza\GiftCard\Model\Api\Data
 */
class TemplateFields extends AbstractExtensibleModel implements TemplateFieldsInterface
{
    /**
     * {@inheritDoc}
     */
    public function getSender()
    {
        return $this->getData(self::SENDER);
    }

    /**
     * {@inheritDoc}
     */
    public function setSender($value)
    {
        return $this->setData(self::SENDER, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getRecipient()
    {
        return $this->getData(self::RECIPIENT);
    }

    /**
     * {@inheritDoc}
     */
    public function setRecipient($value)
    {
        return $this->setData(self::RECIPIENT, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritDoc}
     */
    public function setMessage($value)
    {
        return $this->setData(self::MESSAGE, $value);
    }
}

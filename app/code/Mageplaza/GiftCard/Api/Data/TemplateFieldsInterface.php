<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

namespace Mageplaza\GiftCard\Api\Data;

/**
 * Interface TemplateFieldsInterface
 * @api
 */
interface TemplateFieldsInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const SENDER    = 'sender';
    const RECIPIENT = 'recipient';
    const MESSAGE   = 'message';

    /**
     * @return string
     */
    public function getSender();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSender($value);

    /**
     * @return string
     */
    public function getRecipient();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRecipient($value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMessage($value);
}

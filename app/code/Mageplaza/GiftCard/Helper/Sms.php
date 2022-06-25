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

namespace Mageplaza\GiftCard\Helper;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\GiftCard\Model\GiftCard;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

/**
 * Class Sms
 * @package Mageplaza\GiftCard\Helper
 */
class Sms extends Data
{
    /**
     * @var array
     */
    protected $attributeValue = [];

    /**
     * @param GiftCard $giftCard
     * @param $type
     *
     * @return $this
     * @throws ConfigurationException
     */
    public function sendSms($giftCard, $type)
    {
        if (!$this->isSmsEnable($type) || !$giftCard->getDeliveryAddress()) {
            return $this;
        }

        $storeId = $giftCard->getStoreId();

        $sid    = $this->getSmsConfig('twilio_account_sid', $storeId);
        $token  = $this->getSmsConfig('twilio_account_token', $storeId);
        $sender = $this->getSmsConfig('address_sender', $storeId);

        if (!$sid || !$token || !$sender) {
            return $this;
        }

        $client = new Client($sid, $token);
        $client->messages->create(
            $giftCard->getDeliveryAddress(),
            [
                'from' => $sender,
                'body' => $this->generateMessageContent($giftCard, $type)
            ]
        );

        return $this;
    }

    /**
     * Generate message content
     *
     * @param GiftCard $giftCard
     * @param string $type
     *
     * @return mixed|null
     */
    public function generateMessageContent($giftCard, $type)
    {
        $storeId = $giftCard->getStoreId();
        $message = $this->getSmsConfig($type ? $type . '/content' : 'content', $storeId);

        $patternString = '#\{\{[a-z_]*\}\}#';
        if (preg_match($patternString, $message)) {
            $message = preg_replace_callback(
                $patternString,
                function ($param) use ($giftCard) {
                    return $this->getAttributeValue(trim($param[0], '{}'), $giftCard);
                },
                $message
            );
        }

        return $message;
    }

    /**
     * @param string $attribute
     * @param GiftCard $giftCard
     *
     * @return string
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getAttributeValue($attribute, $giftCard)
    {
        if (!isset($this->attributeValue[$giftCard->getId()])) {
            $templateFields = $giftCard->getTemplateFields() ? self::jsonDecode($giftCard->getTemplateFields()) : [];
            $store          = $this->storeManager->getStore($giftCard->getStoreId());

            $this->attributeValue[$giftCard->getId()] = [
                'sender_name'  => isset($templateFields['sender']) ? $templateFields['sender'] : '',
                'message'      => isset($templateFields['message']) ? $templateFields['message'] : '',
                'code'         => $giftCard->getCode(),
                'balance'      => $this->formatPrice($giftCard->getBalance(), false, $store->getId()),
                'status'       => $giftCard->getStatusLabel(),
                'expired_date' => $this->formatDate($giftCard->getExpiredAt()),
                'store_url'    => $store->getBaseUrl()
            ];
        }

        return $this->attributeValue[$giftCard->getId()][$attribute] ?? '';
    }
}

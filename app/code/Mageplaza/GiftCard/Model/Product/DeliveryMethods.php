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

namespace Mageplaza\GiftCard\Model\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\EmailAddress;
use Magento\Framework\Validator\Timezone;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\Source\FieldRenderer;
use Zend\Validator\Date;

/**
 * Class DeliveryMethods
 * @package Mageplaza\GiftCard\Model\Product
 */
class DeliveryMethods extends AbstractSource
{
    const TYPE_EGIFT   = 1;
    const TYPE_PRINT   = 2;
    const TYPE_MAIL    = 3;
    const METHOD_EMAIL = 1;
    const METHOD_SMS   = 2;
    const METHOD_PRINT = 3;
    const METHOD_POST  = 4;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * @var Timezone
     */
    private $timezone;

    /**
     * DeliveryMethods constructor.
     *
     * @param Data $dataHelper
     * @param Http $request
     * @param EmailAddress $emailAddress
     * @param Timezone $timezone
     */
    public function __construct(
        Data $dataHelper,
        Http $request,
        EmailAddress $emailAddress,
        Timezone $timezone
    ) {
        $this->request      = $request;
        $this->helper       = $dataHelper;
        $this->emailAddress = $emailAddress;
        $this->timezone     = $timezone;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::TYPE_EGIFT => __('eGift'),
            self::TYPE_PRINT => __('Print-at-home'),
            self::TYPE_MAIL  => __('Mail')
        ];
    }

    /**
     * Get all option
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (static::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * @param       $method
     * @param array $params
     *
     * @return array
     */
    public function getDeliveryMethod($method, $params = [])
    {
        if (!array_key_exists($method, self::getOptionArray())) {
            return [];
        }

        $methodLabels = static::getMethodOptionArray();
        switch ($method) {
            case self::TYPE_EGIFT:
                $methods = [
                    [
                        'key'    => self::METHOD_EMAIL,
                        'label'  => $methodLabels[self::METHOD_EMAIL],
                        'fields' => [
                            'email' => [
                                'label'       => __('Email'),
                                'type'        => 'input',
                                'name'        => 'email',
                                'value'       => isset($params['email']) ? $params['email'] : '',
                                'placeHolder' => __('Recipient email'),
                                'class'       => 'validate-email required-entry',
                                'required'    => true
                            ]
                        ]
                    ]
                ];

                if ($this->helper->isSmsEnable()) {
                    $methods[] = [
                        'key'    => self::METHOD_SMS,
                        'label'  => $methodLabels[self::METHOD_SMS],
                        'fields' => [
                            'phone_number' => [
                                'label'       => __('Phone Num.'),
                                'type'        => 'input',
                                'name'        => 'phone_number',
                                'value'       => isset($params['phone_number']) ? $params['phone_number'] : '',
                                'placeHolder' => __('Recipient phone number'),
                                'class'       => 'delivery-phone-number required-entry',
                                'required'    => true,
                                'note'        => __('Enter with country code prefix. Example: +1 for US phone number.')
                            ]
                        ]
                    ];
                }
                break;
            case self::TYPE_PRINT:
                $methods = [
                    [
                        'key'    => self::METHOD_PRINT,
                        'label'  => $methodLabels[self::METHOD_PRINT],
                        'fields' => [
                            'label' => [
                                'label' => '',
                                'type'  => 'label',
                                'name'  => 'print_label',
                                'value' => __('You can print gift card on the confirmation email or the Gift Card in your account.'),
                            ]
                        ]
                    ]
                ];
                break;
            default:
                $methods = [
                    [
                        'key'    => self::METHOD_POST,
                        'label'  => $methodLabels[self::METHOD_POST],
                        'fields' => [
                            'label' => [
                                'label' => '',
                                'type'  => 'label',
                                'name'  => 'post_label',
                                'value' => __('Please input shipping address when checking out.'),
                            ]
                        ]
                    ]
                ];
                break;
        }

        return $methods;
    }

    /**
     * @param $method
     *
     * @return string
     */
    public static function getFormFieldName($method)
    {
        $fieldName = '';
        switch ($method) {
            case self::METHOD_EMAIL:
                $fieldName = 'recipient_email';
                break;
            case self::METHOD_SMS:
                $fieldName = 'recipient_phone';
                break;
            case self::METHOD_PRINT:
                $fieldName = 'customer_email';
                break;
            case self::METHOD_POST:
                $fieldName = 'recipient_address';
                break;
        }

        return $fieldName;
    }

    /**
     * @return array
     */
    public static function getMethodOptionArray()
    {
        return [
            self::METHOD_EMAIL => __('Email'),
            self::METHOD_SMS   => __('Text Message'),
            self::METHOD_PRINT => __('Print At Home'),
            self::METHOD_POST  => __('Post Office'),
        ];
    }

    /**
     * @return array
     */
    public static function getMethodOptionArrayForForm()
    {
        return ['' => __('-- Please Select --')] + self::getMethodOptionArray();
    }

    /**
     * @param int $method
     * @param $fields
     *
     * @return array
     * @throws LocalizedException
     */
    public function validateMethodFields($method, $fields)
    {
        $currentAction = $this->request->getFullActionName();
        if ($currentAction !== 'wishlist_index_add' && !array_key_exists($method, static::getMethodOptionArray())) {
            throw new LocalizedException(__('Delivery method is invalid.'));
        }

        $options = [FieldRenderer::METHOD => $method];
        if ($method === self::METHOD_EMAIL) {
            $email = $fields->getEmail();
            if (!$this->emailAddress->isValid($email)) {
                throw new LocalizedException(__('Please correct the recipient email.'));
            }
            $options[FieldRenderer::ADDRESS] = $email;
        } elseif ($method === self::METHOD_SMS) {
            $options[FieldRenderer::ADDRESS] = $fields->getPhoneNumber();
        }

        if ($deliveryDate = $fields->getDeliveryDate()) {
            $validator = new Date();
            if (!$validator->isValid($deliveryDate)) {
                throw new LocalizedException(__('Please correct the delivery date.'));
            }
            $options[FieldRenderer::DATE] = $deliveryDate;
        }
        if ($timezone = $fields->getTimezone()) {
            if (!$this->timezone->isValid($timezone)) {
                throw new LocalizedException(__('Please correct the timezone.'));
            }
            $options[FieldRenderer::TIMEZONE] = $timezone;
        }

        return $options;
    }
}

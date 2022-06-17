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

namespace Mageplaza\GiftCard\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Boolean;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Mageplaza\GiftCard\Helper\Product;

/**
 * Class GiftCard
 * @package Mageplaza\GiftCard\Ui\DataProvider\Product\Modifier
 */
class GiftCard extends AbstractModifier
{
    /**
     * Gift Product attributes
     */
    const FIELD_GIFT_CODE_PATTERN  = 'gift_code_pattern';
    const FIELD_GIFT_CARD_AMOUNTS  = 'gift_card_amounts';
    const FIELD_ALLOW_AMOUNT_RANGE = 'allow_amount_range';
    const FIELD_MIN_AMOUNT         = 'min_amount';
    const FIELD_MAX_AMOUNT         = 'max_amount';
    const FIELD_EXPIRE_AFTER_DAY   = 'expire_after_day';
    const FIELD_CAN_REDEEM         = 'can_redeem';
    const FIELD_PRICE_RATE         = 'price_rate';
    const FIELD_CONDITIONS         = 'mpgiftcard_conditions';

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var Product
     */
    protected $_productHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $_meta;

    /**
     * GiftCard constructor.
     *
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param Product $productHelper
     * @param RequestInterface $request
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        Product $productHelper,
        RequestInterface $request
    ) {
        $this->locator        = $locator;
        $this->arrayManager   = $arrayManager;
        $this->_productHelper = $productHelper;
        $this->request        = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $storeId = $this->request->getParam('store');
        $modelId = $this->locator->getProduct()->getId();

        $gcData = [
            static::FIELD_GIFT_CODE_PATTERN => $this->_productHelper->getCodePattern($storeId),
            static::FIELD_CAN_REDEEM        => $this->_productHelper->getGeneralConfig('can_redeem', $storeId),
            static::FIELD_EXPIRE_AFTER_DAY  => $this->_productHelper->getExpireAfterDay($storeId)
        ];
        foreach ($gcData as $field => $value) {
            $useConfigValue = $field === self::FIELD_CAN_REDEEM ? Boolean::VALUE_USE_CONFIG : Product::VALUE_USE_CONFIG;
            $isConfigUsed   = isset($data[$modelId][static::DATA_SOURCE_DEFAULT][$field])
                && ($data[$modelId][static::DATA_SOURCE_DEFAULT][$field] === (string) $useConfigValue);

            if ($isConfigUsed || !$modelId) {
                $data[$modelId][static::DATA_SOURCE_DEFAULT][$field]                 = $value;
                $data[$modelId][static::DATA_SOURCE_DEFAULT]['use_config_' . $field] = '1';
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->_meta = $meta;

        $this->customizeTextUseConfigField(static::FIELD_GIFT_CODE_PATTERN);
        $this->customizeTextUseConfigField(static::FIELD_EXPIRE_AFTER_DAY);
        $this->customizeYesNoUseConfigField(static::FIELD_CAN_REDEEM);
        $this->customizeGiftCardAmountsField();
        $this->customizeAmountRange();

        return $this->_meta;
    }

    /**
     * customize allow amount range field
     *
     * @return $this|array
     */
    protected function customizeAmountRange()
    {
        $groupCode = $this->getGroupCodeByField($this->_meta, 'container_' . static::FIELD_ALLOW_AMOUNT_RANGE);
        if (!$groupCode) {
            return $this;
        }

        // allow amount range field
        $containerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_ALLOW_AMOUNT_RANGE,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta   = $this->arrayManager->merge($containerPath, $this->_meta, [
            'children' => [
                static::FIELD_ALLOW_AMOUNT_RANGE => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataScope'         => static::FIELD_ALLOW_AMOUNT_RANGE,
                                'additionalClasses' => 'admin__field-x-small',
                                'component'         => 'Mageplaza_GiftCard/js/form/element/allow-amount-range',
                                'componentType'     => Field::NAME,
                                'prefer'            => 'toggle',
                                'valueMap'          => [
                                    'false' => '0',
                                    'true'  => '1',
                                ],
                                'exports'           => [
                                    'checked' => '${$.parentName}.' . static::FIELD_ALLOW_AMOUNT_RANGE . ':allowAmountRange',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // min amount & max amount field
        $minContainerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_MIN_AMOUNT,
            $this->_meta,
            null,
            'children'
        );
        $maxContainerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_MAX_AMOUNT,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta      = $this->arrayManager->merge($minContainerPath, $this->_meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magento_Ui/js/form/components/group',
                    ],
                ],
            ],
            'children'  => [
                static::FIELD_MIN_AMOUNT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'             => __('Range From'),
                                'additionalClasses' => 'admin__field-small',
                                'scopeLabel'        => __('[WEBSITE]'),
                                'validation'        => [
                                    'validate-greater-than-zero' => true
                                ]
                            ],
                        ],
                    ],
                ]
            ]
        ]);
        $this->_meta      = $this->arrayManager->merge($maxContainerPath, $this->_meta, [
            'children' => [
                static::FIELD_MAX_AMOUNT => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'             => __('To'),
                                'additionalClasses' => 'admin__field-small admin__field-group-show-label',
                                'validation'        => [
                                    'validate-greater-than-zero' => true
                                ]
                            ],
                        ],
                    ],
                ]
            ]
        ]);
        $this->_meta      = $this->arrayManager->set(
            $minContainerPath . '/children/' . static::FIELD_MAX_AMOUNT,
            $this->_meta,
            $this->arrayManager->get(
                $maxContainerPath . '/children/' . static::FIELD_MAX_AMOUNT,
                $this->_meta
            )
        );
        $this->_meta      = $this->arrayManager->remove($maxContainerPath, $this->_meta);

        // price percentage field
        $containerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_PRICE_RATE,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta   = $this->arrayManager->merge($containerPath, $this->_meta, [
            'children' => [
                static::FIELD_PRICE_RATE => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'addbefore'         => '%',
                                'additionalClasses' => 'admin__field-small',
                                'validation'        => ['validate-zero-or-greater' => true],
                                'service'           => false,
                                'disabled'          => false,
                                'globalScope'       => true,
                                'scopeLabel'        => __('[WEBSITE]')
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return $this;
    }

    /**
     * Customize Gift card amount field
     *
     * @return $this
     */
    protected function customizeGiftCardAmountsField()
    {
        $fieldPath = $this->arrayManager->findPath(static::FIELD_GIFT_CARD_AMOUNTS, $this->_meta, null, 'children');
        if (!$fieldPath) {
            return $this;
        }

        $this->_meta = $this->arrayManager->merge($fieldPath, $this->_meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'       => 'dynamicRows',
                        'label'               => __('Gift Card Amount'),
                        'renderDefaultRecord' => false,
                        'recordTemplate'      => 'record',
                        'dataScope'           => '',
                        'dndConfig'           => [
                            'enabled' => false,
                        ]
                    ]
                ]
            ],
            'children'  => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate'    => true,
                                'is_collection' => true,
                                'component'     => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope'     => '',
                                'disabled'      => false,
                            ]
                        ]
                    ],
                    'children'  => [
                        'amount'       => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Price::NAME,
                                        'label'         => __('Amount'),
                                        'dataScope'     => 'amount',
                                        'addbefore'     => $this->getStore()->getBaseCurrency()->getCurrencySymbol()
                                    ]
                                ]
                            ]
                        ],
                        'price'        => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement'   => Input::NAME,
                                        'dataType'      => Price::NAME,
                                        'label'         => __('Price'),
                                        'enableLabel'   => true,
                                        'dataScope'     => 'price',
                                        'addbefore'     => $this->getStore()->getBaseCurrency()->getCurrencySymbol()
                                    ]
                                ]
                            ]
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType'      => Text::NAME,
                                        'label'         => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return $this;
    }

    /**
     * Customize text field use config value
     *
     * @param string $attribute
     *
     * @return $this|array
     */
    protected function customizeTextUseConfigField($attribute)
    {
        $groupCode = $this->getGroupCodeByField($this->_meta, 'container_' . $attribute);
        if (!$groupCode) {
            return $this;
        }

        $validation    = $attribute === self::FIELD_EXPIRE_AFTER_DAY ? ['validate-greater-than-zero' => true] : [];
        $containerPath = $this->arrayManager->findPath('container_' . $attribute, $this->_meta, null, 'children');
        $this->_meta   = $this->arrayManager->merge($containerPath, $this->_meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magento_Ui/js/form/components/group',
                    ],
                ],
            ],
            'children'  => [
                $attribute                 => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataScope'         => $attribute,
                                'additionalClasses' => 'admin__field-medium',
                                'component'         => 'Mageplaza_GiftCard/js/form/element/text-use-config',
                                'componentType'     => Field::NAME,
                                'validation'        => $validation
                            ],
                        ],
                    ],
                ],
                'use_config_' . $attribute => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType'      => 'number',
                                'formElement'   => Checkbox::NAME,
                                'componentType' => Field::NAME,
                                'description'   => __('Use Config Settings'),
                                'dataScope'     => 'use_config_' . $attribute,
                                'valueMap'      => [
                                    'false' => '0',
                                    'true'  => '1',
                                ],
                                'exports'       => [
                                    'checked' => '${$.parentName}.' . $attribute . ':isUseConfig'
                                ],
                                'imports'       => [
                                    'disabled' => '${$.parentName}.' . $attribute . ':isUseDefault'
                                ]
                            ],
                        ],
                    ],
                ],
            ]
        ]);

        return $this;
    }

    /**
     * Customize yes/no use config value field
     *
     * @param string $attribute
     *
     * @return $this
     */
    protected function customizeYesNoUseConfigField($attribute)
    {
        $groupCode = $this->getGroupCodeByField($this->_meta, 'container_' . $attribute);
        if (!$groupCode) {
            return $this;
        }

        $containerPath = $this->arrayManager->findPath('container_' . $attribute, $this->_meta, null, 'children');
        $this->_meta   = $this->arrayManager->merge($containerPath, $this->_meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Magento_Ui/js/form/components/group',
                    ],
                ],
            ],
            'children'  => [
                $attribute                 => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataScope'         => $attribute,
                                'additionalClasses' => 'admin__field-x-small',
                                'component'         => 'Magento_Ui/js/form/element/single-checkbox-use-config',
                                'componentType'     => Field::NAME,
                                'prefer'            => 'toggle',
                                'valueMap'          => [
                                    'false' => '0',
                                    'true'  => '1',
                                ],
                            ],
                        ],
                    ],
                ],
                'use_config_' . $attribute => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'dataType'      => 'number',
                                'formElement'   => Checkbox::NAME,
                                'componentType' => Field::NAME,
                                'description'   => __('Use Config Settings'),
                                'dataScope'     => 'use_config_' . $attribute,
                                'valueMap'      => [
                                    'false' => '0',
                                    'true'  => '1',
                                ],
                                'exports'       => [
                                    'checked' => '${$.parentName}.' . $attribute . ':isUseConfig'
                                ],
                                'imports'       => [
                                    'disabled' => '${$.parentName}.' . $attribute . ':isUseDefault'
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return $this;
    }

    /**
     * @return Store|StoreInterface
     */
    protected function getStore()
    {
        return $this->locator->getStore();
    }
}

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

namespace Mageplaza\GiftCard\Block\Product;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product;
use Magento\Config\Model\Config\Source\Locale\Timezone;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\Product\DeliveryMethods;
use Mageplaza\GiftCard\Model\Source\Status;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class View
 * @package Mageplaza\GiftCard\Block\Product
 */
class View extends AbstractView
{
    /**
     * @var array
     */
    protected $_templates = [];

    /**
     * @var Template
     */
    protected $templateHelper;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * @var Timezone
     */
    protected $timezoneSource;

    /**
     * @var DeliveryMethods
     */
    protected $deliveryMethods;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    private $productHelper;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param TemplateFactory $templateFactory
     * @param Data $dataHelper
     * @param PriceCurrencyInterface $priceCurrency
     * @param LocaleFormat $localeFormat
     * @param Timezone $timezoneSource
     * @param DeliveryMethods $deliveryMethods
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        TemplateFactory $templateFactory,
        Data $dataHelper,
        PriceCurrencyInterface $priceCurrency,
        LocaleFormat $localeFormat,
        Timezone $timezoneSource,
        DeliveryMethods $deliveryMethods,
        \Magento\Catalog\Helper\Product $productHelper,
        array $data = []
    ) {
        $this->templateFactory = $templateFactory;
        $this->dataHelper      = $dataHelper;
        $this->templateHelper  = $dataHelper->getTemplateHelper();
        $this->priceCurrency   = $priceCurrency;
        $this->localeFormat    = $localeFormat;
        $this->timezoneSource  = $timezoneSource;
        $this->deliveryMethods = $deliveryMethods;
        $this->productHelper   = $productHelper;

        parent::__construct($context, $arrayUtils, $data);

        $this->_templates = $this->initTemplates();
    }

    /**
     * @return int
     */
    public function isUseTemplate()
    {
        return count($this->getTemplates());
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->_templates;
    }

    /**
     * @return array
     */
    public function getProductConfig()
    {
        return [
            'information' => $this->prepareInformation(),
            'template'    => $this->_templates
        ];
    }

    /**
     * @return array
     */
    public function prepareInformation()
    {
        $product       = $this->getProduct();
        $deliveryParam = $product->getConfigureMode() || $this->getRequest()->getParam('id')
            ? $product->getPreconfiguredValues()->getData()
            : [];

        $enableDeliveryDate = (int) $product->getGiftCardType() !== DeliveryMethods::TYPE_PRINT
            && $this->dataHelper->getProductConfig('enable_delivery_date');

        $information = [
            'productId'          => $product->getId(),
            'currencyRate'       => $this->priceCurrency->convert(1),
            'priceFormat'        => $this->localeFormat->getPriceFormat(),
            'amounts'            => $product->getGiftCardAmounts() ?: [],
            'delivery'           => $this->deliveryMethods->getDeliveryMethod(
                $product->getGiftCardType(),
                $deliveryParam
            ),
            'enableDeliveryDate' => $enableDeliveryDate,
            'timezone'           => [
                'enable'  => $enableDeliveryDate && $this->dataHelper->getProductConfig('enable_timezone'),
                'options' => $this->timezoneSource->toOptionArray(),
                'value'   => $this->dataHelper->getConfigValue('general/locale/timezone')
            ],
            'fileUploadUrl'      => $this->_urlBuilder->getUrl('mpgiftcard/template/upload'),
            'messageMaxChar'     => $this->dataHelper->getMessageMaxChar(),
            'uploadTooltip'      => __('Acceptable formats are jpg, png and gif.'),
        ];

        if ((boolean) $product->getAllowAmountRange()) {
            $minAmount = $product->getMinAmount();
            $minAmount = (!$minAmount || $minAmount < 0) ? 0 : $minAmount;

            $maxAmount = $product->getMaxAmount();
            $maxAmount = ($maxAmount && $maxAmount < $minAmount) ? $minAmount : $maxAmount;

            $priceRate = $product->getPriceRate() ?: 100;

            $information['openAmount'] = [
                'min'  => $minAmount,
                'max'  => $maxAmount,
                'rate' => $priceRate,
            ];
        }

        return $information;
    }

    /**
     * @return array
     */
    public function initTemplates()
    {
        $resultTemplates = [];
        $templateIds     = $this->getProduct()->getGiftProductTemplate();
        if ($templateIds) {
            $templates = $this->templateFactory->create()
                ->getCollection()
                ->addFieldToFilter('template_id', ['in' => explode(',', $templateIds)])
                ->addFieldToFilter('status', Status::STATUS_ACTIVE);
            foreach ($templates->getItems() as $template) {
                $resultTemplates[$template->getId()] = $this->templateHelper->prepareTemplateData($template->getData());
            }
        }

        return $resultTemplates;
    }

    /**
     * @return array|mixed
     * @throws NoSuchEntityException
     */
    public function getConfigureData()
    {
        $configureData = [];
        /** @var Product $product */
        $product = $this->getProduct();
        if ($product->getConfigureMode() || $this->getRequest()->getParam('id')) {
            $configureData = $product->getPreconfiguredValues()->getData();
        }

        if (!isset($configureData['from']) && ($customer = $this->dataHelper->getCustomer())) {
            $configureData['from'] = $customer->getName();
        }

        return $configureData;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->addPageAsset('jquery/fileUploader/css/jquery.fileupload-ui.css');

        return parent::_prepareLayout();
    }

    /**
     * @return bool|string
     */
    public function getFonts()
    {
        $fonts = '';

        foreach ($this->_templates as $template) {
            if (!in_array($template['font'], ['Arial', 'times', 'helvetica', 'courier'])) {
                $fonts .= $template['font'] . '|';
            }
        }

        return substr($fonts, 0, -1);
    }

    /**
     * @return bool
     */
    public function getSkipSaleableCheck()
    {
        return $this->productHelper->getSkipSaleableCheck();
    }
}

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

use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Config\Model\Config\Structure\Element\Dependency\Field;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Header\Logo;
use Mageplaza\GiftCard\Api\Data\GiftCodeInterface;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\Pool;
use Mageplaza\GiftCard\Model\TemplateFactory;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class Template
 * @package Mageplaza\GiftCard\Helper
 */
class Template extends Data
{
    const TEMPLATE_MEDIA_PATH = 'mageplaza/giftcard';

    /**
     * @var string
     */
    protected $placeHolderImage;

    /**
     * @var ReadInterface
     */
    protected $mediaDirectory;

    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var FieldFactory
     */
    protected $_fieldFactory;

    /**
     * @var Escaper
     */
    protected $_escaper;

    /**
     * @var Logo
     */
    protected $_logo;

    /**
     * Template constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param Filesystem $filesystem
     * @param Repository $assetRepo
     * @param TemplateFactory $templateFactory
     * @param FieldFactory $fieldFactory
     * @param Escaper $escaper
     * @param Logo $logo
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        Filesystem $filesystem,
        Repository $assetRepo,
        TemplateFactory $templateFactory,
        FieldFactory $fieldFactory,
        Escaper $escaper,
        Logo $logo
    ) {
        $this->_logo           = $logo;
        $this->_assetRepo      = $assetRepo;
        $this->templateFactory = $templateFactory;
        $this->_fieldFactory   = $fieldFactory;
        $this->_escaper        = $escaper;
        $this->mediaDirectory  = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        parent::__construct($context, $objectManager, $storeManager, $localeDate);
    }

    /**
     * @return WriteInterface
     */
    public function getMediaDirectory()
    {
        return $this->mediaDirectory;
    }

    /**
     * @param $templateId
     *
     * @return \Mageplaza\GiftCard\Model\Template | null
     */
    public function getTemplateById($templateId)
    {
        return $this->templateFactory->create()->load($templateId);
    }

    /**
     * @param $template
     * @param bool $mergeCss
     * @param bool $includeSampleContent
     * @param bool $isPrint
     *
     * @return array
     */
    public function prepareTemplateData($template, $mergeCss = false, $includeSampleContent = false, $isPrint = false)
    {
        $design = (isset($template['design']) && $template['design'])
            ? self::jsonDecode($template['design'])
            : [];
        if (!count($design) || !isset($design['giftcard'])) {
            return [];
        }

        $images         = [];
        $templateImages = (isset($template['images']) && $template['images'])
            ? self::jsonDecode($template['images'])
            : [];
        foreach ($templateImages as $image) {
            $file = $this->mediaDirectory->getRelativePath($this->getMediaPath($image['file']));
            if ($this->mediaDirectory->isFile($file)) {
                $images[] = [
                    'file' => $image['file'],
                    'src'  => $isPrint
                        ? $this->mediaDirectory->getAbsolutePath($this->getMediaPath($image['file']))
                        : $this->getMediaUrl($image['file']),
                    'alt'  => $image['label'] ?: __('Gift Card Image')
                ];
            }
        }

        $initFields = $this->getTemplateFields();

        foreach ($design as $id => &$field) {
            $css = [];
            foreach ($field as $key => $value) {
                if (!in_array($key, ['width', 'height', 'top', 'left'])) {
                    $css = ($key === 'css') ? $value : [];
                    unset($field[$key]);
                }
            }
            $field = array_merge($css, $field);

            // Add css for giftcard
            if ($id === 'giftcard') {
                if (isset($template['text_color']) && $template['text_color']) {
                    $field['color'] = '#' . trim($template['text_color'], '#');
                }

                $background = '';
                if (isset($template['background_color']) && $template['background_color']) {
                    $background .= '#' . trim($template['background_color'], '#');
                }
                if (isset($template['background_image']) && $template['background_image']) {
                    $bgImage    = str_replace(self::TEMPLATE_MEDIA_PATH, '', $template['background_image']);
                    $background .= ' url(' . ($isPrint ? $this->mediaDirectory->getAbsolutePath($this->getMediaPath($bgImage)) : $this->getMediaUrl($bgImage)) . ') no-repeat left top';
                }
                if ($background) {
                    $field['background'] = $background;
                }
            }

            // Merge css into 1 field
            if ($mergeCss) {
                $css = '';
                if ($isPrint && isset($field['padding'])) {
                    $field['height'] -= (float) $field['padding'] * 2;
                    $field['width']  -= (float) $field['padding'] * 2;
                }

                foreach ($field as $key => $value) {
                    $css .= $key . ': ' . $value;
                    if (in_array($key, ['width', 'height', 'top', 'left'])) {
                        $css .= 'px';
                    }
                    $css .= '; ';
                }
                $field = [
                    'css' => $css
                ];
            } else {
                foreach ($field as $key => $value) {
                    if (in_array($key, ['width', 'height', 'top', 'left'])) {
                        $field[$key] = $value . 'px';
                    }
                }
                $field = [
                    'css' => $field
                ];
            }

            if ($includeSampleContent && isset($initFields[$id]['sampleContent'])) {
                $field['label'] = $initFields[$id]['sampleContent'];
            }

            switch ($id) {
                case 'image':
                    if (count($images)) {
                        $field['src'] = $images[0]['src'];
                    } else {
                        unset($design[$id]);
                    }
                    break;
                case 'logo':
                    $logo = $this->getTemplateConfig('logo');
                    if ($logo) {
                        $field['src'] = $isPrint
                            ? $this->mediaDirectory->getAbsolutePath($this->getMediaPath($logo))
                            : $this->getMediaUrl($logo);
                    } else {
                        unset($design[$id]);
                    }
                    break;
                case 'barcode':
                    $field['src'] = $this->_assetRepo->getUrl('Mageplaza_GiftCard::images/barcode.png');
                    break;
                case 'title':
                    if (isset($template['title']) && $template['title']) {
                        $field['label'] = $template['title'];
                    }
                    break;
                case 'note':
                    if (isset($template['note']) && $template['note']) {
                        $field['label'] = $template['note'];
                    }
            }
            $field['key'] = $id;
        }
        unset($field);
        $card = array_shift($design);

        return [
            'id'        => (int) $template['template_id'],
            'name'      => $template['name'],
            'title'     => $template['title'],
            'canUpload' => (bool) $template['can_upload'],
            'card'      => $card,
            'design'    => $design,
            'images'    => $images,
            'font'      => isset($template['font_family']) ? $template['font_family'] : '',
        ];
    }

    /**
     * @param GiftCard|GiftCard[]|GiftCodeInterface[] $giftCard
     * @param string $outputType
     * @param null $fileName
     *
     * @return null|string
     * @throws Html2PdfException
     * @throws NoSuchEntityException
     */
    public function outputGiftCardPdf($giftCard, $outputType = 'I', $fileName = null)
    {
        if ($fileName === null) {
            $fileName = 'gift_card_' . time() . '.pdf';
        }

        $html2pdf = new Html2Pdf();
        $DS       = DIRECTORY_SEPARATOR;

        if (is_array($giftCard)) {
            $isContent = false;
            $page      = 0;
            foreach ($giftCard as $item) {
                $content = $this->generateGiftCardHTML($item, true);
                if ($content) {
                    $fontPath = dirname(__DIR__) . $DS . 'Fonts' . $DS . 'GoogleFonts' . $DS
                        . $item->getTemplateFont() . '.php';
                    if (file_exists($fontPath)) {
                        $html2pdf->addFont($item->getTemplateFont(), '', $fontPath);
                    } else {
                        $html2pdf->setDefaultFont($item->getTemplateFont());
                    }

                    $html2pdf->_INDEX_NewPage($page);
                    $html2pdf->writeHTML($content);
                    $isContent = true;
                }
            }
            if ($isContent) {
                $html2pdf->pdf->SetDisplayMode('fullpage');

                return $html2pdf->output($fileName, $outputType);
            }
        } else {
            $content = $this->generateGiftCardHTML($giftCard, true);
            if ($content) {
                $fontPath = dirname(__DIR__) . $DS . 'Fonts' . $DS . 'GoogleFonts' . $DS
                    . $giftCard->getTemplateFont() . '.php';
                if (file_exists($fontPath)) {
                    $html2pdf->addFont($giftCard->getTemplateFont(), '', $fontPath);
                } else {
                    $html2pdf->setDefaultFont($giftCard->getTemplateFont());
                }
                $html2pdf->writeHTML($content);

                return $html2pdf->output($fileName, $outputType);
            }
        }

        return null;
    }

    /**
     * @param GiftCard $giftCard
     * @param bool $isPrint
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function generateGiftCardHTML($giftCard, $isPrint = false)
    {
        $html = '';
        if (!$giftCard->getTemplateId()) {
            return $html;
        }

        $template = $this->getTemplateById($giftCard->getTemplateId());
        if (!$template || !$template->getId()) {
            return $html;
        }

        $templateFields = $this->prepareTemplateData($template, true, true, $isPrint);
        if (count($templateFields)) {
            $design = $templateFields['design'];

            //Init gift card data
            if ($templateData = $giftCard->getTemplateFields()) {
                $sendData = self::jsonDecode($templateData);
                if (isset($design['message'])) {
                    $design['message']['label'] = isset($sendData['message']) ? $sendData['message'] : '';
                }
                if (isset($design['from'])) {
                    $design['from']['label'] = isset($sendData['sender']) ? __('From: %1', $sendData['sender']) : '';
                }
                if (isset($design['to'])) {
                    $design['to']['label'] = isset($sendData['recipient']) ? __('To: %1', $sendData['recipient']) : '';
                }
            }

            if (isset($design['image']) && ($image = $giftCard->getImage())) {
                $file = $this->mediaDirectory->getRelativePath($this->getMediaPath($image));
                if ($this->mediaDirectory->isFile($file)) {
                    $design['image']['src'] = $this->mediaDirectory->getAbsolutePath($this->getMediaPath($image));
                }
            }

            if (isset($design['barcode'])) {
                $design['barcode']['label'] = $this->getBarcodeImage($giftCard->getCode(), $design['barcode']['css']);
                unset($design['barcode']['src']);
            }

            if (isset($design['code'])) {
                $design['code']['label'] = $giftCard->getCode();
            }

            $store = $this->storeManager->getStore($giftCard->getStoreId());
            if (isset($design['value'])) {
                $balance   = $giftCard->getBalance();
                $precision = (($balance - floor($balance)) > 0.0001) ? PriceCurrencyInterface::DEFAULT_PRECISION : 0;

                $design['value']['label'] = $this->getPriceCurrency()->convertAndFormat(
                    $balance,
                    false,
                    $precision,
                    $store
                );
            }

            if (isset($design['expired-date'])) {
                if ($expiredAt = $giftCard->getExpiredAt()) {
                    $design['expired-date']['label'] = __('Expired Date: %1', date('M d, Y', strtotime($expiredAt)));
                } else {
                    unset($design['expired-date']);
                }
            }

            $html .= '<div style="margin-top: 50px; position: relative; margin: auto; overflow: hidden; border: 1px solid #ccc;' . $templateFields['card']['css'] . '">';
            foreach ($design as $field) {
                $html .= '<div style="position: absolute; overflow: hidden; box-sizing: border-box;' . $field['css'] . '">';
                if (isset($field['src'])) {
                    $html .= '<img src="' . $field['src'] . '" style="max-width: 100%; max-height: 100%;"/>';
                } else {
                    $html .= $field['label'];
                }
                $html .= '</div>';
            }
            $html .= '</div>';

            $font = $templateFields['font'] ?: 'times';

            $giftCard->setTemplateFont(str_replace(' ', '', strtolower($font)));
        }

        return $html;
    }

    /**
     * @param $code
     * @param $barcodeCss
     *
     * @return string
     */
    protected function getBarcodeImage($code, $barcodeCss)
    {
        $style = [];
        $css   = explode(';', $barcodeCss);
        foreach ($css as $attribute) {
            $att = explode(':', trim($attribute));
            if (count($att) === 2) {
                $style[trim($att[0])] = trim($att[1]);
            }
        }

        $width    = isset($style['width']) ? trim($style['width'], 'px') * 0.264583333 : 30;
        $height   = isset($style['height']) ? trim($style['height'], 'px') * 0.264583333 : 6;
        $color    = isset($style['color']) ? $style['color'] : '#000000';
        $fontSize = isset($style['font-size']) ? trim($style['font-size'], 'px') * 0.264583333 : '4';

        return "<barcode dimension='1D' type='C128' value='{$code}' label='none' style='width:{$width}mm; height:{$height}mm; color: {$color}; font-size: {$fontSize}mm;'></barcode>";
    }

    /**
     * Template design fields
     *
     * @return array
     */
    public function getTemplateFields()
    {
        return [
            'image'        => [
                'label' => __('Image'),
                'img'   => $this->_assetRepo->getUrl('Mageplaza_GiftCard::images/default.png')
            ],
            'logo'         => [
                'label' => __('Logo'),
                'img'   => $this->_logo->getLogoSrc()
            ],
            'title'        => [
                'label'         => __('Title'),
                'sampleContent' => 'Gift Card',
                'css'           => [
                    'font-size' => '28px'
                ]
            ],
            'from'         => [
                'label'         => __('From'),
                'sampleContent' => 'From: John',
                'css'           => [
                    'font-size' => '13px'
                ]
            ],
            'to'           => [
                'label'         => __('To'),
                'sampleContent' => 'To: Marry',
                'css'           => [
                    'font-size' => '13px'
                ]
            ],
            'message'      => [
                'label'         => __('Message'),
                'sampleContent' => 'Hope you enjoy this gift card!',
                'css'           => [
                    'border-radius'    => '5px',
                    'border'           => '1px solid #ccc',
                    'background-color' => '#fff',
                    'font-size'        => '15px',
                    'color'            => '#000'
                ]
            ],
            'value'        => [
                'label'         => __('Value'),
                'sampleContent' => '$100',
                'css'           => [
                    'font-size'   => '28px',
                    'font-weight' => 'bold'
                ]
            ],
            'code'         => [
                'label'         => __('Code'),
                'sampleContent' => 'XXXX-XXXX-XXXX',
                'css'           => [
                    'font-size'   => '15px',
                    'font-weight' => 'bold'
                ]
            ],
            'barcode'      => [
                'label' => __('Barcode'),
                'img'   => $this->_assetRepo->getUrl('Mageplaza_GiftCard::images/barcode.png'),
                'css'   => [
                    'background-color' => '#fff',
                    'padding'          => '5px'
                ]
            ],
            'note'         => [
                'label'         => __('Note'),
                'sampleContent' => 'This is sample content for gift card note',
                'css'           => [
                    'background-color' => '#fff',
                    'font-size'        => '9px'
                ]
            ],
            'expired-date' => [
                'label'         => __('Expired Date'),
                'sampleContent' => 'Expired Date: 15th Jan, 2018',
                'css'           => [
                    'background-color' => '#fff',
                    'font-size'        => '10px'
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getPlaceHolderImage()
    {
        if ($this->placeHolderImage === null) {
            $this->placeHolderImage = $this->_assetRepo->getUrl(
                'Magento_Catalog::images/product/placeholder/image.jpg'
            );
        }

        return $this->placeHolderImage;
    }

    /*********************************** GIFT CARD MEDIA PATH / PROCESS IMAGES********************************
     * Filesystem directory path of temporary product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return self::TEMPLATE_MEDIA_PATH . '/tmp';
    }

    /**
     * Part of URL of temporary product images
     * relatively to media folder
     *
     * @param string $file
     *
     * @return string
     */
    public function getTmpMediaPath($file)
    {
        return $this->getBaseTmpMediaPath() . '/' . $this->_prepareFile($file);
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        try {
            return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseTmpMediaPath();
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e);

            return '';
        }
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return self::TEMPLATE_MEDIA_PATH;
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function getMediaPath($file)
    {
        return self::TEMPLATE_MEDIA_PATH . '/' . $this->_prepareFile($file);
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        try {
            return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseMediaPath();
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e);

            return '';
        }
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * Get filename which is not duplicated with other files in media temporary and media directories
     *
     * @param string $fileName
     * @param string $descriptionPath
     *
     * @return string
     */
    public function getNotDuplicatedFilename($fileName, $descriptionPath)
    {
        $fileMediaName = $descriptionPath . '/'
            . Uploader::getNewFileName($this->mediaDirectory->getAbsolutePath($this->getMediaPath($fileName)));

        if ($fileMediaName !== $fileName) {
            return $this->getNotDuplicatedFilename($fileMediaName, $descriptionPath);
        }

        return $fileMediaName;
    }

    /********************************************** Prepare for Admin form *******************************************/
    /**
     * @param GiftCard|Pool $model
     * @param Fieldset $fieldset
     * @param Dependence $dependencies
     *
     * @return $this
     */
    public function getTemplateFieldSet($model, $fieldset, $dependencies)
    {
        /** @var array $templateData */
        $templateData = $this->prepareTemplateFormData();

        $fieldset->addField('template_id', 'select', [
            'name'   => 'template_id',
            'label'  => __('Template'),
            'title'  => __('Template'),
            'values' => isset($templateData['options']) ? $templateData['options'] : []
        ]);
        $fieldset->addField('image', 'note', [
            'label' => __('Image'),
            'title' => __('Image'),
            'text'  => $this->getImageHtml($templateData, $model)
        ]);
        $fieldset->addField('sender', 'text', [
            'name'  => 'template_fields[sender]',
            'label' => __('Sender Name'),
            'title' => __('Sender Name')
        ]);
        $fieldset->addField('recipient', 'text', [
            'name'  => 'template_fields[recipient]',
            'label' => __('Recipient Name'),
            'title' => __('Recipient Name')
        ]);
        $fieldset->addField('message', 'textarea', [
            'name'  => 'template_fields[message]',
            'label' => __('Message'),
            'title' => __('Message')
        ]);

        $dependencies->addFieldMap('template_id', 'template_id')
            ->addFieldMap('image', 'image')
            ->addFieldMap('message', 'message')
            ->addFieldDependence(
                'image',
                'template_id',
                $this->getRefField(isset($templateData['image']) ? $templateData['image'] : '')
            )->addFieldDependence(
                'message',
                'template_id',
                $this->getRefField(isset($templateData['message']) ? $templateData['message'] : '')
            );

        return $this;
    }

    /**
     * @return array
     */
    protected function prepareTemplateFormData()
    {
        $templateArray      = ['options' => ['value' => '', 'label' => __('-- Please Select --')]];
        $templateCollection = $this->templateFactory->create()->getCollection();
        foreach ($templateCollection as $template) {
            $templateArray['options'][] = ['value' => $template->getId(), 'label' => $template->getName()];
            $templateData               = $this->prepareTemplateData($template);
            if (!empty($templateData['images'])) {
                $templateArray['image'][] = $template->getId();
            }
            if (isset($templateData['design']['message'])) {
                $templateArray['message'][] = $template->getId();
            }
            $templateArray['template'][$template->getId()] = $templateData;
        }

        return $templateArray;
    }

    /**
     * @param $data
     * @param GiftCard $model
     *
     * @return string
     */
    protected function getImageHtml($data, $model)
    {
        $data = isset($data['template']) ? $data['template'] : [];

        $file = $this->mediaDirectory->getRelativePath($this->getMediaPath($model->getImage()));
        if ($this->mediaDirectory->isFile($file)) {
            $imageSrc  = $model->getImage();
            $imageFile = $this->getMediaUrl($imageSrc);
        }

        $imgSrc  = isset($imageSrc) ? $imageSrc : '';
        $imgFile = isset($imageFile) ? $imageFile : '';
        $dataTmp = $this->_escaper->escapeHtml(self::jsonEncode($data));

        $html = '<div class="giftcard-thumbnail-preview">';
        $html .= '<input type="hidden" name="image" id="template_image" data-src="' . $imgSrc . '" data-url="' . $imgFile . '"/>';
        $html .= '<div class="thumbnail-preview-content template-image-content" data-template="' . $dataTmp . '">';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Reference Field for dependencies
     *
     * @param $value
     *
     * @return Field
     */
    public function getRefField($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        return $this->_fieldFactory->create([
            'fieldData'   => ['value' => $value, 'separator' => ','],
            'fieldPrefix' => ''
        ]);
    }

    /**
     * @return string
     */
    public function getFormScript()
    {
        $currencySymbols = self::jsonEncode($this->getCurrencyCodes());

        return "
        require(['jquery'], function ($) {
            var templateEl = $('#template_id'),
                imageEl = $('#template_image'),
                defaultImage = imageEl.data('src'),
                imageContainer = $('.template-image-content'),
                templateData = imageContainer.data('template');
            
            templateEl.on('change', function(){
                imageContainer.html('');
                imageEl.val('');
                if(!templateData.hasOwnProperty($(this).val())){
                    return this;
                }
                var images = templateData[$(this).val()]['images'];
                
                //check if image is uploaded or be removed from template
                if(defaultImage){
                    var imageSelected = $.grep(images, function(image){
                        return image.file == defaultImage;
                    });

                    if(imageSelected.length == 0){
                        images.push({
                            file: defaultImage,
                            src: imageEl.data('url')
                        });
                    }
                }
                
                $.each(images, function(index, value){
                     var element = $('<div></div>', {class: 'thumbnail-image'}),
                         image = $('<img />', {
                             class: 'thumbnail-preview-image',
                             src: value.src,
                             'data-src': value.file
                         });
                     imageContainer.append(element.html(image));
                });
                
                initImagePreview();
            });
            templateEl.trigger('change');
    
            function initImagePreview(){
                var thumbnailEl = $('.thumbnail-preview-image'),
                    hasSaveImage = false;
                $.each(thumbnailEl, function () {
                    var self = $(this);
                    self.on('click', function () {
                        imageEl.val(self.data('src'));
                        thumbnailEl.closest('.thumbnail-image').removeClass('active');
                        self.closest('.thumbnail-image').addClass('active');
                    });
                    if(defaultImage == self.data('src')){
                        self.trigger('click');
                        hasSaveImage = true;
                    }
                });
                if(!hasSaveImage){
                    thumbnailEl.first().trigger('click');
                }
            }
            
            var currencySymbols = {$currencySymbols},
                storeEl = $('#store_id'),
                labelEl = $('.field-balance label.addafter');
            if(storeEl.length){
                storeEl.on('change', function(){
                    if(!currencySymbols.hasOwnProperty($(this).val())){
                        return this;
                    }
                    labelEl.html(currencySymbols[$(this).val()]);
                });
                storeEl.trigger('change');
            }
		});";
    }

    /**
     * @return array
     */
    protected function getCurrencyCodes()
    {
        $currencySymbols = [];
        $stores          = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $currencySymbols[$store->getId()] = $store->getBaseCurrency()->getCurrencySymbol();
        }

        return $currencySymbols;
    }
}

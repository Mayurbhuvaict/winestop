<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */

namespace Magetrend\PdfTemplates\Model\Pdf\Element\Items\Renderer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Default item pdf renderer
 *
 * @category MageTrend
 * @package  Magetend/PdfTemplates
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-pdf-invoice-pro
 */
class DefaultRenderer
{
    /**
     * @var \Magento\Sales\Model\Order\Invoice\Item|null
     */
    public $item = null;

    /**
     * @var \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote|null
     */
    public $source = null;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    public $taxData;

    /**
     * @var \Magetrend\PdfTemplates\Model\Pdf\Decorator
     */
    public $decorator;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    public $imageBuilder;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    public $image;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    public $assetRepo;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    public $productRepository;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    public $fileDriver;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    public $imageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * @var \Magetrend\PdfTemplates\Helper\Data
     */
    public $moduleHelper;

    public $quoteItemFactory;

    public $serializer;

    /**
     * DefaultRenderer constructor.
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magetrend\PdfTemplates\Model\Pdf\Decorator $decorator
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Catalog\Helper\Image $image
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magetrend\PdfTemplates\Model\Pdf\Decorator $decorator,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Catalog\Helper\Image $image,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magetrend\PdfTemplates\Helper\Data $moduleHelper,
        \Magento\Sales\Model\Order\ItemFactory $quoteItemFactory
    ) {
        $this->taxData = $taxData;
        $this->decorator = $decorator;
        $this->imageBuilder = $imageBuilder;
        $this->image = $image;
        $this->assetRepo = $assetRepo;
        $this->productRepository = $productRepository;
        $this->fileDriver = $fileDriver;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->moduleHelper = $moduleHelper;
        $this->quoteItemFactory = $quoteItemFactory;
    }

    /**
     * Set Item
     *
     * @param \Magento\Sales\Model\Order\Invoice\Item $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get Item
     *
     * @return \Magento\Sales\Model\AbstractModel
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set Source
     *
     * @param \Magento\Sales\Model\Order\Invoice\Item $item
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get Source
     *
     * @return \Magento\Sales\Model\AbstractModel|\Magento\Quote\Model\Quote
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get Order
     *
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder()
    {
        $source = $this->getSource();
        if ($source instanceof \Magento\Sales\Model\Order) {
            return $source;
        }

        if ($source instanceof \Magento\Quote\Model\Quote) {
            return $source;
        }

        return $source->getOrder();
    }

    /**
     * Returns formated item price
     *
     * @return string
     */
    public function getFormatedItemPrice()
    {
        $priceForDisplay = $this->getItemPricesForDisplay();
        return $priceForDisplay[0]['formated_price'];
    }

    /**
     * Returns formated subtotal
     *
     * @return string
     */
    public function getFormatedSubtotal()
    {
        $priceForDisplay = $this->getItemPricesForDisplay();
        return $priceForDisplay[0]['formated_subtotal'];
    }

    /**
     * Returns item prices
     *
     * @return array
     */
    public function getItemPricesForDisplay()
    {
        $item = $this->getItem();

        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            return $this->getQuoteItemPricesForDisplay();
        }

        $order = $this->getOrder();
        if ($this->taxData->displaySalesBothPrices()) {
            $prices = [
                [
                    'label' => __('Incl. Tax') . ':',
                    'formated_price' => $order->formatPriceTxt($item->getPriceInclTax()),
                    'price' => $item->getPriceInclTax(),
                    'formated_subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax()),
                    'subtotal' => $item->getRowTotalInclTax(),
                ],
            ];
        } elseif ($this->taxData->displaySalesPriceInclTax()) {
            $prices = [
                [
                    'formated_price' => $order->formatPriceTxt($item->getPriceInclTax()),
                    'price' => $item->getPriceInclTax(),
                    'formated_subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax()),
                    'subtotal' => $item->getRowTotalInclTax(),
                ],
            ];
        } else {
            $prices = [
                [
                    'formated_price' => $order->formatPriceTxt($item->getPrice()),
                    'price' => $item->getPrice(),
                    'formated_subtotal' => $order->formatPriceTxt($item->getRowTotal()),
                    'subtotal' => $item->getRowTotal(),
                ],
            ];
        }
        return $prices;
    }

    public function getQuoteItemPricesForDisplay()
    {
        $item = $this->getItem();
        $currencyCode = $this->moduleHelper->getCurrencyCode($item->getStoreId());
        if ($this->taxData->displaySalesBothPrices()) {
            $prices = [
                [
                    'label' => __('Incl. Tax') . ':',
                    'formated_price' => $this->moduleHelper->formatPrice($currencyCode, $item->getPriceInclTax()),
                    'price' => $item->getPriceInclTax(),
                    'formated_subtotal' => $this->moduleHelper->formatPrice($currencyCode, $item->getRowTotalInclTax()),
                    'subtotal' => $item->getRowTotalInclTax(),
                ],
            ];
        } elseif ($this->taxData->displaySalesPriceInclTax()) {
            $prices = [
                [
                    'formated_price' => $this->moduleHelper->formatPrice($currencyCode, $item->getPriceInclTax()),
                    'price' => $item->getPriceInclTax(),
                    'formated_subtotal' => $this->moduleHelper->formatPrice($currencyCode, $item->getRowTotalInclTax()),
                    'subtotal' => $item->getRowTotalInclTax(),
                ],
            ];
        } else {
            $prices = [
                [
                    'formated_price' => $this->moduleHelper->formatPrice($currencyCode, $item->getPrice()),
                    'price' => $item->getPrice(),
                    'formated_subtotal' => $this->moduleHelper->formatPrice($currencyCode, $item->getRowTotal()),
                    'subtotal' => $item->getRowTotal(),
                ],
            ];
        }
        return $prices;
    }

    public function getItemOptions()
    {
        $result = [];
        $item = $this->getItem();
        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            $options = $this->getQuoteItemOptions($item);
        } else {
            $options = $this->getOrderItemOptions($item);
        }

        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    public function getQuoteItemOptions($item)
    {
        return $item->getProductOptions();
    }

    public function getOrderItemOptions($item)
    {
        return $this->getOrderItem($item)->getProductOptions();
    }

    public function getFormatedItemOptions()
    {
        $result = $this->getItemOptions();
        if (empty($result)) {
            return '';
        }

        $optionsString = '';
        foreach ($result as $option) {
            $label = strip_tags($option['label']);
            $optionsString.= $label;

            if (isset($option['value'])) {
                $value = $this->decorator->addDecorator(
                    $option['value'],
                    \Magetrend\PdfTemplates\Model\Pdf\Decorator::TYPE_COLOR,
                    'table_row_product_line_2_value_color'
                );

                $value = strip_tags($value);
                $optionsString.= ': '.$value;
            }

            $optionsString.= ', ';
        }

        return rtrim($optionsString, ', ');
    }

    public function getOrderItem($item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            return $item;
        }

        if ($item instanceof \Magento\Quote\Model\Quote\Item) {
            return $item;
        }

        return $item->getOrderItem();
    }

    public function getItemImage($attributes)
    {
        $item = $this->getItem();
        try {
            $product = $this->productRepository->getById($this->getProductIdImage(), false, 0);
        } catch (NoSuchEntityException $e) {
            return $this->getImagePlaceholder();
        }

        if (!$product->getId() || $product->getThumbnail() == '') {
            return $this->getImagePlaceholder();
        }

        $thumb = trim($product->getThumbnail(), '/');
        $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath('catalog/product/'. $thumb);

        if (!$this->fileDriver->isExists($absolutePath)) {
            return $this->getImagePlaceholder();
        }

        $destination = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $destination->create();

        $ext = explode('.', $product->getThumbnail());
        $ext = end($ext);
        $uniqueId = uniqid(\Magento\Framework\Math\Random::getRandomNumber()) . time() . '.'.$ext;
        $destinationPath = $destination->getAbsolutePath('pdftemplates_'.$uniqueId);

        if (isset($attributes['image_width']) && is_numeric($attributes['image_width'])) {
            $imagewidth = $attributes['image_width']*2;
        } else {
            $imagewidth = 100;
        }

        $imageResize = $this->imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(false);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($imagewidth);
        $imageResize->save($destinationPath);

        return $destinationPath;
    }

    public function getProductIdImage()
    {
        $item = $this->getItem();
        return $item->getProductId();
    }

    public function getImagePlaceholder()
    {
        $assestRepository = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\View\Asset\Repository::class);
        $placeHolder = $assestRepository->createAsset('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
        return $placeHolder->getSourceFile();
    }
}
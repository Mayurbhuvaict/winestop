<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Model;

/**
 * Simple google shopping data feed model
 */
class Feeds extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var int
     */
    const ALL_GROUPS = 32000;
    /**
     * @var bool
     */
    public $isCron = false;
    /**
     * @var int
     */
    private $_counter = 0;
    /**
     * @var \Wyomind\Framework\Helper\Progress
     */
    protected $progressHelper;
    /**
     * @var \Magento\Store\Model\StoreFactory|null
     */
    public $storeFactory = null;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory|null
     */
    public $categoryFactory = null;
    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory|null
     */
    public $attributeTypeFactory = null;
    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory|null
     */
    public $attributeFactory = null;
    /**
     * @var null|ResourceModel\Product\CollectionFactory
     */
    public $productCollectionFactory = null;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory|null
     */
    public $attributeOptionValueCollectionFactory = null;
    /**
     * @var \Magento\Framework\View\Asset\ContextInterface|\Magento\Framework\View\Asset\File\FallbackContext|null
     */
    public $staticContext = null;
    /**
     * @var null|ResourceModel\Functions\CollectionFactory
     */
    public $functionCollectionFactory = null;
    /**
     * @var null|ResourceModel\TaxClass
     */
    private $_taxClassResourceModel = null;
    /**
     * @var null|ResourceModel\Images
     */
    private $_imagesResourceModel = null;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|null
     */
    private $_ioWrite = null;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|null
     */
    private $_ioRead = null;
    /* System */
    /**
     * @var int
     */
    public $inc = 0;
    /**
     * @var
     */
    public $limit = INF;
    /**
     * @var bool
     */
    public $isPreview = false;
    /**
     * @var int
     */
    private $_sqlSize = 1500;
    /**
     * @var string
     */
    private $_charset = "ISO";
    /**
     * @var array
     */
    private $_condition = ["eq" => "= '%s'", "neq" => "!= '%s'", "gteq" => ">= '%s'", "lteq" => "<= '%s'", "gt" => "> '%s'", "lt" => "< '%s'", "like" => "like '%s'", "nlike" => "not like '%s'", "null" => "is null", "notnull" => "is not null", "in" => "in(%s)", "nin" => "not in(%s)"];
    // config
    /**
     * @var string
     */
    private $_baseUrl = "";
    /**
     * @var string
     */
    private $_allowedCurrencies = "";
    /**
     * @var int
     */
    private $_itemInPreview = 10;
    /**
     * @var bool
     */
    private $_includeInMenu = false;
    // params
    /**
     * @var array
     */
    public $params = [];
    // for attributes processing
    /**
     * @var string
     */
    public $backorders = "";
    // qty
    /**
     * @var string
     */
    public $manageStock = "";
    /**
     * @var array
     */
    public $configurableQty = [];
    // images
    /**
     * @var string
     */
    public $defaultImage = "";
    /**
     * @var string
     */
    public $baseImg = "";
    /**
     * @var array
     */
    public $gallery = [];
    // url
    /**
     * @var string
     */
    public $storeUrl = "";
    /**
     * @var int
     */
    public $urlRewrites = -1;
    // prices
    /**
     * @var string
     */
    public $priceIncludesTax = "";
    /**
     * @var string
     */
    public $defaultCurrency = "";
    /**
     * @var array
     */
    public $listOfCurrencies = [];
    /**
     * @var array
     */
    public $taxRates = [];
    // categories
    /**
     * @var string
     */
    public $rootCategory = "";
    /**
     * @var array
     */
    public $categoriesFilterList = [];
    /**
     * @var array
     */
    public $categoriesMapping = [];
    /**
     * @var array
     */
    public $categories = [];
    /**
     * @var array
     */
    public $listOfAttributes = [];
    /**
     * @var array
     */
    public $listOfAttributesType = [];
    /**
     * @var array
     */
    public $attributesLabelsList = [];
    /**
     * @var array
     */
    public $configurable = [];
    /**
     * @var string
     */
    private $_output = "";
    /**
     * @var array
     */
    private $_attributesRequired = [];
    /**
     * @var array
     */
    private $_grouped = [];
    /**
     * @var array
     */
    private $_bundle = [];
    // requirements
    /**
     * @var bool
     */
    private $_requiresConfigurable = false;
    /**
     * @var bool
     */
    private $_requiresBundle = false;
    /**
     * @var bool
     */
    private $_requiresGrouped = false;
    /**
     * @var bool
     */
    private $_loadOptions = false;
    /**
     * @var bool
     */
    private $_loadImages = false;
    /**
     * @var bool
     */
    private $_loadConfigurableQty = false;
    // report
    /**
     * @var array
     */
    public $errorReport = [];
    /**
     * @var array
     */
    private $_flagErrorReport = [];
    /**
     * @var string
     */
    private $_flagDir = "/var/tmp/";
    /**
     * @var string
     */
    private $_flagFile = "";
    /**
     * @var ResourceModel\InventoryStock
     */
    private $_inventoryStockResourceModel;
    /**
     * Collection of all stock inventory data
     * @var array|false
     */
    public $inventoryStock = false;
    /**
     * If the stock
     * @var int
     */
    public $stockId = 1;
    /**
     * Prefix of model events names
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'simplegoogleshopping';
    /**
     * Name of object id field
     * {@inheritdoc}
     */
    protected $_idFieldName = 'simplegoogleshopping_id';
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attributeOptionValueCollectionFactory, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Functions\CollectionFactory $functionCollectionFactory, \Magento\Store\Model\StoreFactory $storeFactory, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Eav\Model\Entity\TypeFactory $attributeTypeFactory, \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\TaxClassFactory $taxClassResourceModelFactory, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\ImagesFactory $imagesResourceModelFactory, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\InventoryStockFactory $inventoryStockResourceModelFactory, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->staticContext = $this->assetRepo->getStaticViewFileContext();
        $this->progressHelper = $this->objectManager->create('Wyomind\\SimpleGoogleShopping\\Helper\\Progress');
        $this->attributeOptionValueCollectionFactory = $attributeOptionValueCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->functionCollectionFactory = $functionCollectionFactory;
        $this->storeFactory = $storeFactory;
        $this->categoryFactory = $categoryFactory;
        $this->attributeTypeFactory = $attributeTypeFactory;
        $this->attributeFactory = $attributeFactory;
        $this->_ioWrite = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $this->_ioRead = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $this->taxClassResourceModelFactory = $taxClassResourceModelFactory;
        $this->_imagesResourceModel = $imagesResourceModelFactory->create();
        $this->_taxClassResourceModel = $taxClassResourceModelFactory->create();
        $this->_inventoryStockResourceModel = $inventoryStockResourceModelFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->attributesHelper->setModel($this);
    }
    /**
     * Internal constructor/initializer
     */
    public function _construct()
    {
        $this->_init("Wyomind\\SimpleGoogleShopping\\Model\\ResourceModel\\Feeds");
    }
    /**
     * Load custom functions from DB and instantiate them
     * @throws \Exception
     */
    public function loadCustomFunctions()
    {
        $displayErrors = ini_get("display_errors");
        ini_set("display_errors", 0);
        $collection = $this->functionCollectionFactory->create();
        foreach ($collection as $function) {
            if ($this->attributesHelper->execPhp($function->getScript(), "?>" . $function->getScript()) === false) {
                if ($this->isPreview) {
                    ini_set("display_errors", $displayErrors);
                    throw new \Exception("Syntax error in " . $function->getScript() . " : " . error_get_last()["message"]);
                } else {
                    ini_set("display_errors", $displayErrors);
                    $this->messageManager->addError("Syntax error in <i>" . $function->getScript() . "</i><br>" . error_get_last()["message"]);
                    throw new \Exception();
                }
            }
        }
        ini_set("display_errors", $displayErrors);
    }
    /**
     * Generate google shopping data feed
     * @param object $request
     * @return string|\Wyomind\SimpleGoogleShopping\Model\Feeds
     * @throws \Exception
     */
    public function generateXml($request = null, $report = false)
    {
        try {
            if (php_sapi_name() != "cli") {
                session_write_close();
            }
            $connection = $this->getResource()->getConnection();
            $connection->query("SET SESSION group_concat_max_len = 10000;");
            $timeStart = time();
            $globalTime = 0;
            $this->progressHelper->startObservingProgress($this->checkLogFlag(), $this->getId(), $this->getSimplegoogleshoppingTitle());
            $this->progressHelper->log("******************************* NEW PROCESS ******************************************", !$this->isPreview);
            /* retrieve optional parameters from request or model */
            $this->extractParams($request);
            $this->progressHelper->log(">> Parameters loaded", !$this->isPreview);
            // Associate the Helper just now because we need the Helper parameters
            $this->attributesHelper->setModel($this);
            $this->storeManager->setCurrentStore($this->params["store_id"]);
            /* config variables */
            $this->extractConfiguration();
            $this->progressHelper->log(">> Configuration loaded", !$this->isPreview);
            $this->progressHelper->log(">> START PROCESS FOR '" . strtoupper($this->getSimplegoogleshoppingTitle()) . "'", !$this->isPreview);
            /* set the memory limit size */
            $memoryLimit = $this->licenseHelper->getStoreConfig("simplegoogleshopping/system/memorylimit");
            ini_set("memory_limit", $memoryLimit . "M");
            $this->progressHelper->log(">> Memory limit set to '" . (int) $memoryLimit, !$this->isPreview);
            /* open destination file */
            if (!$this->isPreview) {
                $io = $this->openDestinationFile();
            }
            $this->progressHelper->log(">> File created ", !$this->isPreview);
            $display = "";
            /* Data Feed Headers */
            $this->_output = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\"><channel>";
            $this->_output .= "<title>" . $this->params["simplegoogleshopping_title"] . "</title>
";
            $this->_output .= "<link>" . $this->params["simplegoogleshopping_url"] . "</link>
";
            $this->_output .= "<description>" . $this->params["simplegoogleshopping_description"] . "</description>
";
            $this->progressHelper->log(">> Headers added", !$this->isPreview);
            if ($this->isPreview) {
                $display = $this->encode($this->_output);
            } else {
                $io->write($this->encode($this->_output));
                $this->progressHelper->log(">> File is now locked(processing)", !$this->isPreview, \Wyomind\Framework\Helper\Progress::PROCESSING, 0);
            }
            $this->progressHelper->log(">> Flag set on PROCESSING", !$this->isPreview);
            $this->_output = "";
            /* load custom functions */
            if (!$this->isCron) {
                $this->loadCustomFunctions();
                $this->progressHelper->log('Custom functions loaded', !$this->isPreview);
            }
            /* initialize store manager */
            $this->storeManager->setCurrentStore($this->params['store_id']);
            $this->progressHelper->log(">> Current store set on " . $this->params['store_id'], !$this->isPreview);
            /* retrieve data feed categories configuration + all categories available in Magento */
            $this->extractCategories();
            $this->progressHelper->log(">> Categories extracted", !$this->isPreview);
            /* analyze template to find what are the required attributes */
            $attributeCalls = $this->analyzeProductTemplate();
            $this->progressHelper->log(">> Template analyzed", !$this->isPreview);
            /* get entity type id for products */
            $typeId = $this->getEntityTypeId();
            $this->progressHelper->log(">> EntityTypeIds collected", !$this->isPreview);
            /* retrieve all attributes data */
            $this->extractAttributeList($typeId);
            $this->progressHelper->log(">> Attribute list collected", !$this->isPreview);
            /* retrieve tax rates */
            $this->taxRates = $this->_taxClassResourceModel->getTaxRates();
            $this->progressHelper->log(">> Tax rates collected", !$this->isPreview);
            /* extract images */
            if ($this->_loadImages) {
                $this->_imagesResourceModel->setStoreId($this->params["store_id"]);
                $this->gallery = $this->_imagesResourceModel->getImages();
            }
            $this->progressHelper->log(">> Images collected", !$this->isPreview);
            /* retrieve all attributes data */
            if ($this->dataHelper->isMsiEnabled()) {
                /**Get all stocks*/
                $websiteId = $this->storeManager->getStore($this->getStoreId())->getWebsiteId();
                $this->inventoryStock = $this->_inventoryStockResourceModel->create()->collect();
                $stockByWebsiteResolver = $this->objectManager->create("\\Magento\\InventorySales\\Model\\StockByWebsiteIdResolver");
                $this->stockId = $stockByWebsiteResolver->execute($websiteId)->getStockId();
                $this->progressHelper->log(">> Stock inventory collected", !$this->isPreview);
            }
            switch ($this->urlRewrites) {
                case \Wyomind\SimpleGoogleShopping\Model\Config\UrlRewrite::PRODUCT_URL:
                    $notLike = " AND url.target_path = concat('catalog/product/view/id/', e.entity_id)";
                    $concat = "MAX";
                    break;
                default:
                    $notLike = " AND url.target_path LIKE concat('catalog/product/view/id/', e.entity_id, '/category/%')";
                    $concat = "GROUP_CONCAT";
                    break;
            }
            /* extract configurable product + children association */
            if ($this->_requiresConfigurable) {
                $this->configurable = $this->productCollectionFactory->create()->getConfigurableProducts($this->params['store_id'], $notLike, $concat, $this->listOfAttributes);
            }
            $this->progressHelper->log(">> Configurable product collected", !$this->isPreview);
            /* extract configurable quantities */
            if ($this->_loadConfigurableQty) {
                $this->configurableQty = $this->productCollectionFactory->create()->getConfigurableQuantities($this->params['store_id']);
            }
            $this->progressHelper->log(">> Qty for configurable prices collected", !$this->isPreview);
            /* extract bundle products */
            if ($this->_requiresBundle) {
                $this->_bundle = $this->productCollectionFactory->create()->getBundleProducts($this->params['store_id'], $notLike, $concat, $this->listOfAttributes);
            }
            $this->progressHelper->log(">> Bundle products collected", !$this->isPreview);
            /* extract grouped products */
            if ($this->_requiresGrouped) {
                $this->_grouped = $this->productCollectionFactory->create()->getGroupedProducts($this->params['store_id'], $notLike, $concat, $this->listOfAttributes);
            }
            $this->progressHelper->log(">> Grouped products collected", !$this->isPreview);
            /* create main request to retrieve products */
            $mainCollection = $this->productCollectionFactory->create()->getMainRequest($this->params['store_id'], $this->storeManager->getStore()->getWebsiteId(), $notLike, $concat, $this->manageStock, $this->listOfAttributes, $this->categoriesFilterList, $this->_condition, $this->params);
            $this->progressHelper->log(">> Main query built", !$this->isPreview);
            /* ***************************************************************** */
            /* Extract all products                                              */
            /* ***************************************************************** */
            $currentLoop = 0;
            $this->inc = 0;
            // number of products to extract
            if ($this->limit != INF && $this->limit > 0) {
                // if limit is set
                $this->_counter = $this->limit;
            } else {
                $this->_counter = $this->productCollectionFactory->create()->getProductCount($this->params['store_id'], $this->storeManager->getStore()->getWebsiteId(), $notLike, $concat, $this->manageStock, $this->listOfAttributes, $this->categoriesFilterList, $this->_condition, $this->params);
                $this->limit = $this->_counter;
            }
            $loops = ceil($this->_counter / $this->_sqlSize);
            $inc = 1;
            $this->progressHelper->log(">> Total items calculated(" . $this->_counter . " in " . $loops . " queries )", !$this->isPreview);
            $i = 0;
            while ($currentLoop < $loops) {
                $i++;
                // limit the number of product in the result
                $mainCollection->setLimit($this->_sqlSize, $currentLoop);
                $mainCollection->clear();
                $currentLoop++;
                $limitTo = $this->_sqlSize * $currentLoop;
                if ($this->_sqlSize * $currentLoop > $this->_counter) {
                    $limitTo = $this->_counter;
                }
                $this->progressHelper->log(">> Fetching products from " . ($this->_sqlSize * ($currentLoop - 1) + 1) . " to " . $limitTo . "  - iteration #{$currentLoop}", !$this->isPreview, \Wyomind\Framework\Helper\Progress::PROCESSING, round(100 * $i / $this->_counter));
                // Product-by-product treatment
                foreach ($mainCollection as $product) {
                    $this->attributesHelper->skip(false);
                    /*  Initial pattern */
                    $productPattern = $this->params["simplegoogleshopping_xmlitempattern"];
                    foreach ($attributeCalls as $pattern => $attributeCall) {
                        $attributeCallCount = count($attributeCall);
                        if ($attributeCallCount == 0) {
                            continue;
                        }
                        // si product.load_options => duplication of pattern
                        if ($this->_loadOptions && $attributeCall[0]["property"] == "load_options") {
                            $productPattern = $this->attributesHelper->loadOptions($this, $product, $attributeCall[0]["parameters"], $productPattern);
                        }
                        $value = "";
                        for ($j = 0; $j < $attributeCallCount; $j++) {
                            $value = $this->attributesHelper->executeAttribute($attributeCall[$j], $product);
                            if ($attributeCall[$j]["or"] && !empty($value)) {
                                break;
                            }
                        }
                        $underscore = strpos($pattern, "_");
                        $pattern = substr($pattern, $underscore + 1);
                        if (strpos($pattern, "PHP_") === 0) {
                            $pattern = substr($pattern, 4);
                            $value = '"' . str_replace('"', '\\"', $value) . '"';
                        }
                        $productPattern = $this->dataHelper->strReplaceFirst($pattern, $value, $productPattern);
                    }
                    $productPattern = $this->attributesHelper->executePhpScripts($this->isPreview, $productPattern, $product);
                    if (!$this->attributesHelper->getSkip()) {
                        $productPattern = $this->encode($productPattern);
                        $productPattern = $this->xmlEncloseData($productPattern);
                        $productPattern = str_replace(["__LOWERTHAN__", "__HIGHERTHAN__", "__QUOTES__", "__BACKSLASH__"], ["<", ">", '"', "\\", "{", "}"], $productPattern);
                    } else {
                        continue;
                    }
                    // Data output foreach product
                    if (!empty($productPattern)) {
                        $this->_output .= "<item>
";
                        $this->_output .= $productPattern . "
";
                        $this->_output .= "</item>
";
                        $this->inc = $inc;
                        $inc++;
                        $this->report($productPattern);
                    }
                    // Write output to file or display
                    if ($this->isPreview) {
                        $display .= $this->_output;
                        $this->_output = '';
                    } else {
                        if ($inc % (int) $this->licenseHelper->getStoreConfig("simplegoogleshopping/system/buffer") == 0) {
                            $io->write($this->_output);
                            $this->_output = '';
                            $timeEnd = time();
                            $time = (int) $timeEnd - (int) $timeStart;
                            $globalTime += $time;
                            $timeStart = time();
                            $this->progressHelper->log($this->inc . "/" . $this->_counter . " items added", !$this->isPreview, \Wyomind\Framework\Helper\Progress::PROCESSING, round(100 * $i / $this->_counter));
                        }
                    }
                    // if limit is reached then break extraction process
                    if ($this->inc >= $this->limit) {
                        break 2;
                    }
                }
                // for each product
            }
            // while
            unset($mainCollection);
            $this->_output .= "</channel>" . "
";
            $this->_output .= "</rss>";
            $this->progressHelper->log($this->inc . "/" . $this->_counter . " items added", !$this->isPreview);
            if (!$this->isPreview) {
                $io->write($this->_output);
            }
            $this->progressHelper->log(">> Export complete", !$this->isPreview, \Wyomind\Framework\Helper\Progress::SUCCEEDED, 100);
            if (!$this->isPreview) {
                $io->close();
            }
            $display .= $this->_output;
            if ($this->isPreview) {
                if ($report) {
                    return $this->errorReport;
                }
                return $display;
            } else {
                $this->setSimplegoogleshoppingTime($this->coreDate->gmtDate("Y-m-d H:i:s"));
                $this->errorReport["stats"] = [$inc - 1, $globalTime];
                $serialize = "serialize";
                $this->setSimplegoogleshopping_report($serialize($this->errorReport));
                $this->setActionTypeHistory('generate');
                $this->save();
            }
            $this->progressHelper->stopObservingProgress();
            return $this;
        } catch (\Exception $e) {
            $this->progressHelper->log($e->getMessage(), !$this->isPreview, \Wyomind\Framework\Helper\Progress::FAILED);
            throw new \Exception($e->getMessage());
        }
    }
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /* CORE FUNCTIONS                                                           */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /**
     * Generate errors report
     * @param string $productPattern
     */
    private function report($productPattern)
    {
        $treated = [];
        // preliminary check
        try {
            $item = new \SimpleXMLElement("<item>" . str_replace(["<g:", "</g:"], ["<g_", "</g_"], $productPattern) . "</item>");
        } catch (\Exception $e) {
            $this->addToReport("", "xml-error", "");
            return;
        }
        foreach ($this->requirementsHelper->getRequirements() as $properties) {
            if (isset($properties["depends"]) && is_array($properties["depends"])) {
                $rtn = false;
                foreach ($properties["depends"] as $depend => $condition) {
                    foreach ($condition as $statement => $value) {
                        $elt = $this->getRequirementElt($depend);
                        switch ($statement) {
                            case "eq":
                                if ($item->{$elt['tag']} == $value) {
                                    $rtn = true;
                                } else {
                                    $rtn = false;
                                }
                                break;
                            case "neq":
                                if ($item->{$elt['tag']} != $value) {
                                    $rtn = true;
                                } else {
                                    $rtn = false;
                                }
                                break;
                            case "like":
                                if (stristr($item->{$elt['tag']}, $value)) {
                                    $rtn = true;
                                } else {
                                    $rtn = false;
                                }
                                break;
                        }
                    }
                }
            } else {
                $rtn = true;
            }
            if ($rtn == true && !in_array($properties["label"], $treated)) {
                $treated[] = $properties["label"];
                if ($properties["required"] && empty($item->{$properties["tag"]})) {
                    $this->addToReport($properties["label"], "required", $item->g_id);
                } elseif (isset($properties["recommended"]) && empty($item->{$properties["tag"]})) {
                    $this->addToReport($properties["label"], "recommended", $item->g_id, null);
                } elseif ((int) $properties["occurrence"] < count($item->{$properties["tag"]})) {
                    $this->addToReport($properties["label"], "toomany", $item->g_id);
                } else {
                    if (isset($properties["length"]) && strlen($item->{$properties["tag"]}) > $properties["length"]) {
                        $this->addToReport($properties["label"], "toolong", $item->g_id, $properties["length"]);
                    }
                    if (isset($properties["type"]) && $properties["type"] == "RegExp" && !empty($item->{$properties["tag"]}) && !preg_match("/^" . $properties["regexp"] . "\$/i", $item->{$properties["tag"]})) {
                        $this->addToReport($properties["label"], "invalid", $item->g_id, $properties["say"]);
                    } elseif (isset($properties["type"]) && ($properties["type"] == "GoogleProductCategory" || $properties["type"] == "Text")) {
                        continue;
                    } elseif (isset($properties["type"]) && !empty($item->{$properties["tag"]}) && $properties["type"] != "RegExp") {
                        switch ($properties["type"]) {
                            case "Boolean":
                                $regExp = "/^true|false\$/i";
                                break;
                            case "Alphanumeric":
                                $regExp = "/^[\\w\\s\\-]+\$/";
                                break;
                            case "Url":
                                $regExp = "/^http(s)?:\\/\\/.+\$/";
                                break;
                            case "Price":
                                $regExp = "/^([0-9]+\\.[0-9]{2})\\s?[A-Z]{3}\$/";
                                break;
                        }
                        $masks = [];
                        if (!preg_match($regExp, $item->{$properties["tag"]}, $masks)) {
                            $this->addToReport($properties["label"], "invalid", $item->g_id, $properties["type"]);
                        } elseif ($properties["type"] == "Price" && (float) $masks[1] < 0.01) {
                            $this->addToReport($properties["label"], "invalid", $item->g_id, $properties["type"]);
                        }
                    }
                }
            }
        }
    }
    /**
     * @return string
     */
    private function getRequirementElt($depend)
    {
        foreach ($this->requirementsHelper->getRequirements() as $r) {
            if ($r["label"] == $depend) {
                return $r;
            }
        }
    }
    /**
     * Add a product to the error report
     * @param string $element
     * @param string $type
     * @param string $sku
     * @param string $additional
     */
    private function addToReport($element, $type, $sku, $additional = null)
    {
        $required = __("Missing required attribute");
        $recommended = __("Missing recommended attribute");
        $toomany = __("Too many attribute ");
        $toolong = __("Attribute value too long");
        $invalid = __("Invalid attribute value");
        $notParsable = __("XML parsing error");
        if ($type == 'xml-error') {
            $message = $notParsable . " : " . $element;
        }
        if ($type == "required") {
            $message = $required . " : " . $element;
        }
        if ($type == "recommended") {
            $message = $recommended . " : " . $element;
        }
        if ($type == "toomany") {
            $message = $toomany . " : " . $element;
        }
        if ($type == "toolong") {
            $message = $toolong . " : " . $element . "(" . $additional . " " . __("symbols maximum") . ")";
        }
        if ($type == "invalid") {
            $message = $invalid . " : " . $element . "(" . $additional . " " . __("expected") . ")";
        }
        $this->_flagErrorReport[$type . "_" . $element][] = (string) $sku;
        if (count($this->_flagErrorReport[$type . "_" . $element]) > 1 && count($this->_flagErrorReport[$type . "_" . $element]) <= $this->_itemInPreview) {
            $this->errorReport[$type][$element]["count"]++;
            if ($sku != "") {
                $this->errorReport[$type][$element]["skus"] .= ", " . (string) $sku;
            }
        } elseif (count($this->_flagErrorReport[$type . "_" . $element]) > $this->_itemInPreview) {
            $this->errorReport[$type][$element]["count"]++;
        } else {
            $this->errorReport[$type][$element] = ["message" => $message, "count" => 1];
            if ($sku != "" && isset($this->errorReport[$type]) && isset($this->errorReport[$type][$element]) && isset($this->errorReport[$type][$element]["skus"])) {
                $this->errorReport[$type][$element]["skus"] .= (string) $sku;
            } else {
                $this->errorReport[$type][$element]["skus"] = (string) $sku;
            }
        }
    }
    /**
     * Open the destination file of the data feed if needed
     * @return \Magento\Framework\Filesystem\File\WriteInterface|null
     * @throws \Exception
     */
    public function openDestinationFile()
    {
        $io = null;
        $this->_ioWrite->create($this->getSimplegoogleshoppingPath());
        // create path if not exists
        if (!is_writable($this->getPath())) {
            throw new \Exception(__('File "%1" cannot be saved.<br/>Please, make sure the directory "%2" is writeable by web server.', $this->getSimplegoogleshoppingFilename(), $this->getPath()));
        } else {
            $io = $this->_ioWrite->openFile($this->getSimplegoogleshoppingPath() . "/" . $this->getSimplegoogleshoppingFilename(), "w");
        }
        return $io;
    }
    /**
     * Get the parent product if needed
     * @param string $reference
     * @param \Mage\Core\Catalog\Product $product
     * @return object
     */
    public function checkReference($reference, $product)
    {
        if (($reference == "parent" || $reference == "configurable") && isset($this->configurable[$product->getId()])) {
            return $this->configurable[$product->getId()];
        } elseif (($reference == "parent" || $reference == "grouped") && isset($this->_grouped[$product->getId()])) {
            return $this->_grouped[$product->getId()];
        } elseif (($reference == "parent" || $reference == "bundle") && isset($this->_bundle[$product->getId()])) {
            return $this->_bundle[$product->getId()];
        } elseif ($reference == "product") {
            return $product;
        } else {
            return null;
        }
    }
    /**
     * Retrieve params from the request or from the model itself
     * @param $request
     */
    private function extractParams($request)
    {
        $resource = $this->appResource;
        $read = $resource->getConnection("core_read");
        $table = $resource->getTableName('simplegoogleshopping_feeds');
        $fields = $read->describeTable($table);
        foreach (array_keys($fields) as $field) {
            $this->params[$field] = $request !== null && (is_string($request->getParam($field)) || is_array($request->getParam($field))) ? $request->getParam($field) : $this->getData($field);
        }
        $this->progressHelper->log(">> Parameters collected", !$this->isPreview);
    }
    /**
     * Retrieve the store configuration
     */
    private function extractConfiguration()
    {
        $this->logEnabled = $this->licenseHelper->getStoreConfig("simplegoogleshopping/system/log");
        $this->urlRewrites = $this->licenseHelper->getStoreConfig("simplegoogleshopping/system/urlrewrite");
        $this->defaultImage = $this->licenseHelper->getStoreConfig("catalog/placeholder/image_placeholder");
        if ($this->licenseHelper->getDefaultConfig('catalog/price/scope') == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
            $this->defaultCurrency = $this->licenseHelper->getStoreConfig("currency/options/base", $this->params['store_id']);
        } else {
            $this->defaultCurrency = $this->licenseHelper->getStoreConfig("currency/options/base", 0);
        }
        $this->manageStock = $this->licenseHelper->getStoreConfig("cataloginventory/item_options/manage_stock");
        $this->backorders = $this->licenseHelper->getStoreConfig("cataloginventory/item_options/backorders");
        $this->_sqlSize = $this->licenseHelper->getStoreConfig("simplegoogleshopping/system/sqlsize");
        $this->_includeInMenu = $this->licenseHelper->getStoreConfig("simplegoogleshopping/system/include_in_menu");
        $this->_baseUrl = $this->getStoreBaseUrl();
        $this->storeUrl = $this->getStoreUrl($this->params["store_id"]);
        $this->baseImg = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, false);
        $this->priceIncludesTax = $this->licenseHelper->getStoreConfig(\Magento\Tax\Model\Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX);
        $this->rootCategory = $this->storeManager->getStore()->getRootCategoryId();
        $this->_allowedCurrencies = $this->currency->getConfigAllowCurrencies();
        $this->listOfCurrencies = $this->currency->getCurrencyRates($this->defaultCurrency, array_values($this->_allowedCurrencies));
        $this->_itemInPreview = $this->licenseHelper->getStoreConfig("simplegoogleshopping/system/preview");
    }
    /**
     * Retrieve categories data(check/not checked, mapping, id)
     * + retrieve all categories available in Magento
     */
    private function extractCategories()
    {
        // 1. data feed configuration data
        $this->categoriesFilterList = [];
        $this->categoriesMapping = [];
        if (is_array(json_decode($this->params['simplegoogleshopping_categories'], true))) {
            foreach (json_decode($this->params['simplegoogleshopping_categories'], true) as $key => $categoriesFilter) {
                if (isset($categoriesFilter["c"]) && $categoriesFilter["c"] == 1) {
                    $this->categoriesFilterList[] = $key;
                }
            }
            foreach (json_decode($this->params['simplegoogleshopping_categories'], true) as $key => $categoriesFilter) {
                if ($categoriesFilter["m"] != "") {
                    $this->categoriesMapping[$key] = $categoriesFilter["m"];
                }
            }
        }
        if (count($this->categoriesFilterList) < 1) {
            $this->categoriesFilterList[] = "*";
        }
        // 2. all categories available
        $listOfCategories = $this->categoryFactory->create()->getCollection()->setStoreId($this->params["store_id"])->addAttributeToSelect(["name", "store_id", "is_active", "include_in_menu"]);
        $this->categories = [];
        foreach ($listOfCategories as $category) {
            $this->categories[$category->getId()]["name"] = $category->getName();
            $this->categories[$category->getId()]["path"] = $category->getPath();
            $this->categories[$category->getId()]["level"] = $category->getLevel();
            if ($this->_includeInMenu) {
                $this->categories[$category->getId()]["include_in_menu"] = true;
            } else {
                $this->categories[$category->getId()]["include_in_menu"] = $category->getIncludeInMenu();
            }
        }
        foreach ($this->categoriesFilterList as $i => $id) {
            if ($id != '*' && !array_key_exists($id, $this->categories)) {
                unset($this->categoriesFilterList[$i]);
            }
        }
        if (!count($this->categoriesFilterList)) {
            $this->categoriesFilterList = [0 => '*'];
        }
        $this->categoriesFilterList = array_values($this->categoriesFilterList);
    }
    /**
     * Analyse product template, then check what attributes are required
     */
    private function analyzeProductTemplate()
    {
        $result = $this->parserHelper->extractAttributeCalls($this->params["simplegoogleshopping_xmlitempattern"]);
        $this->_requiresConfigurable = false;
        $this->_requiresBundle = false;
        $this->_requiresGrouped = false;
        // check needed parent types & needed product attributes
        foreach ($result as $infos) {
            foreach ($infos as $info) {
                // check needed parent types
                switch ($info["object"]) {
                    case "parent":
                        $this->_requiresConfigurable = true;
                        $this->_requiresBundle = true;
                        $this->_requiresGrouped = true;
                        break;
                    case "configurable":
                        $this->_requiresConfigurable = true;
                        break;
                    case "bundle":
                        $this->_requiresBundle = true;
                        break;
                    case "grouped":
                        $this->_requiresGrouped = true;
                        break;
                }
                // check if statements
                if (isset($info['parameters']['if'])) {
                    foreach ($info['parameters']['if'] as $if) {
                        if (isset($if['object'])) {
                            switch ($if['object']) {
                                case 'parent':
                                    $this->_requiresConfigurable = true;
                                    $this->_requiresBundle = true;
                                    $this->_requiresGrouped = true;
                                    break;
                                case 'configurable':
                                    $this->_requiresConfigurable = true;
                                    break;
                                case 'bundle':
                                    $this->_requiresBundle = true;
                                    break;
                                case 'grouped':
                                    $this->_requiresGrouped = true;
                                    break;
                            }
                        }
                        if (isset($if['property'])) {
                            array_push($this->_attributesRequired, $if['property']);
                        }
                    }
                }
                // check product attributes
                switch ($info["property"]) {
                    case "load_options":
                    case "use_option":
                        $this->_loadOptions = true;
                        break;
                    case "image":
                        $this->_loadImages = true;
                        break;
                    case "url":
                    case "uri":
                        array_push($this->_attributesRequired, "url_key");
                        break;
                    case "image_link":
                        array_push($this->_attributesRequired, "image");
                        array_push($this->_attributesRequired, "small_image");
                        array_push($this->_attributesRequired, "thumbnail");
                        if (isset($info['parameters']['role'])) {
                            array_push($this->_attributesRequired, $info['parameters']['role']);
                        }
                        $this->_loadImages = true;
                        break;
                    case "availability":
                    case 'is_in_stock':
                    case 'qty':
                        if ($info["object"] == "configurable" || $this->params["simplegoogleshopping_type_ids"] == "*" || strpos($this->params["simplegoogleshopping_type_ids"], "configurable") !== false) {
                            $this->_loadConfigurableQty = true;
                        }
                        break;
                    case "sc_images":
                        array_push($this->_attributesRequired, "image");
                        array_push($this->_attributesRequired, "small_image");
                        array_push($this->_attributesRequired, "thumbnail");
                        break;
                    case "sc_description":
                        array_push($this->_attributesRequired, "description");
                        array_push($this->_attributesRequired, "short_description");
                        array_push($this->_attributesRequired, "manufacturer");
                        array_push($this->_attributesRequired, "name");
                        array_push($this->_attributesRequired, "sku");
                        break;
                    case "sc_ean":
                        array_push($this->_attributesRequired, "ean");
                        break;
                    case "sc_url":
                        array_push($this->_attributesRequired, "url_key");
                        array_push($this->_attributesRequired, "url");
                        break;
                    default:
                        array_push($this->_attributesRequired, $info["property"]);
                }
            }
        }
        $this->_attributesRequired = array_unique($this->_attributesRequired);
        return $result;
    }
    /**
     * Retrieve all attributes to use + attributes label
     * @param $typeId
     */
    private function extractAttributeList($typeId)
    {
        // Attribute list from the database
        $attributesList = $this->attributeFactory->create()->getCollection()->addFieldToFilter("entity_type_id", ["eq" => $typeId]);
        // List of attributes required and available in the database
        $this->listOfAttributes = [];
        $this->listOfAttributesType = [];
        foreach ($attributesList as $key => $attr) {
            if (in_array($attr['attribute_code'], $this->_attributesRequired)) {
                array_push($this->listOfAttributes, $attr['attribute_code']);
                $this->listOfAttributesType[$attr['attribute_code']] = $attr['frontend_input'];
            }
        }
        // Add essential attributes to the list
        if (!in_array("special_price", $this->listOfAttributes)) {
            $this->listOfAttributes[] = "special_price";
        }
        if (!in_array("special_from_date", $this->listOfAttributes)) {
            $this->listOfAttributes[] = "special_from_date";
        }
        if (!in_array("special_to_date", $this->listOfAttributes)) {
            $this->listOfAttributes[] = "special_to_date";
        }
        if (!in_array("price_type", $this->listOfAttributes)) {
            $this->listOfAttributes[] = "price_type";
        }
        if (!in_array("price", $this->listOfAttributes)) {
            $this->listOfAttributes[] = "price";
        }
        $this->listOfAttributes[] = "tax_class_id";
        // Add attributes to filter
        foreach (json_decode($this->params["simplegoogleshopping_attributes"]) as $attributeFilter) {
            if (!in_array($attributeFilter->code, $this->listOfAttributes) && $attributeFilter->checked) {
                if (!in_array($attributeFilter->code, ["is_in_stock", "qty", "entity_id", "created_at", "updated_at", 'min_price'])) {
                    $this->listOfAttributes[] = $attributeFilter->code;
                }
            }
        }
        /* Extraction des labels pour les attributes */
        $attributeLabels = $this->attributeOptionValueCollectionFactory->create();
        $attributeLabels->setStoreFilter($this->params["store_id"])->addOrder("option_id", \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC)->addOrder("tdv.store_id", \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC)->getData();
        $this->attributesLabelsList = [];
        foreach ($attributeLabels as $attributeLabel) {
            $this->attributesLabelsList[$attributeLabel["option_id"]][$this->params["store_id"]] = $attributeLabel["value"];
        }
    }
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /* FILE SYSTEM UTILITIES                                                  */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /**
     * Get the absolute root dir of the magento install
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getAbsoluteRootDir()
    {
        // $rootDirectory = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        $rootDirectory = BP;
        // a verifier pour Magento Cloud (semble provoquer d'autres problemes)
        /*if (substr($rootDirectory, 0, 5) == '/app/' || substr($rootDirectory, 0, 5) == "\/app") {
              $rootDirectory = str_replace(array('/app/', "\/app"), '', $rootDirectory);
          }*/
        return $rootDirectory;
    }
    /**
     * Get full path of the data feed
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getPath()
    {
        return str_replace("//", "/", $this->getAbsoluteRootDir() . $this->getSimplegoogleshoppingPath());
    }
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /* TEMPLATING UTILITIES                                                   */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /**
     * Enclose xml data into CDATA
     * @param string $productPattern
     * @param bool $enclose
     * @return string
     */
    private function xmlEncloseData($productPattern, $enclose = true)
    {
        $pattern = "/(<[^>!\\/]+>)([^<]*)(<\\/[^>]+>)/Us";
        //$pattern = '/(?<opening><([^>!\/]+)>)(?<content>.*)(?<closing><\/\2+>)/Us';
        $matches = [];
        preg_match_all($pattern, $productPattern, $matches);
        foreach (array_keys($matches[1]) as $key) {
            $tagContent = trim($matches[2][$key]);
            if (empty($tagContent) && !is_numeric($tagContent)) {
                $productPattern = str_replace($matches[0][$key], "", $productPattern);
            } else {
                if ($enclose && strpos($tagContent, "<![CDATA[") === false) {
                    $productPattern = str_replace($matches[0][$key], $matches[1][$key] . "<![CDATA[" . $tagContent . "]]>" . $matches[3][$key], $productPattern);
                    //$productPattern = str_replace($matches[0][$key], ($matches['opening'][$key]) . '<![CDATA[' . $tagContent . ']]>' . ($matches['closing'][$key]), $productPattern);
                } else {
                    $productPattern = str_replace($matches[0][$key], $matches[1][$key] . $tagContent . $matches[3][$key], $productPattern);
                }
            }
        }
        $a = preg_split("/
/s", $productPattern);
        $o = "";
        foreach ($a as $line) {
            strlen(trim($line)) > 0 ? $o .= $line . "
" : false;
        }
        return $o;
    }
    /**
     * Encode to uft8 or not
     * @param string $productPattern
     * @return string
     */
    private function encode($productPattern)
    {
        if ($this->isPreview) {
            return $productPattern;
        } else {
            if ($this->_charset == "UTF-8") {
                return utf8_decode($productPattern);
            } else {
                return $productPattern;
            }
        }
    }
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /* BASIC UTILITIES                                                        */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /**
     * Get store url
     * @param int $storeId
     * @return string
     */
    public function getStoreUrl($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->getStoreId();
        }
        return $this->storeFactory->create()->load($storeId)->getBaseUrl();
    }
    /**
     * Get store base url
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, false);
    }
    /**
     *  In popup ?
     * @param bool $d
     */
    public function setDisplay($d)
    {
        $this->isPreview = $d;
    }
    /**
     * @return int
     */
    private function getEntityTypeId()
    {
        $typeId = -1;
        $resTypeId = $this->attributeTypeFactory->create()->getCollection()->addFieldToFilter("entity_type_code", ["eq" => "catalog_product"]);
        foreach ($resTypeId as $re) {
            $typeId = $re['entity_type_id'];
        }
        return $typeId;
    }
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /* DEBUG UTILITIES                                                        */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    private function checkLogFlag()
    {
        return $this->licenseHelper->getStoreConfig('simplegoogleshopping/system/log') ? true : false;
    }
}
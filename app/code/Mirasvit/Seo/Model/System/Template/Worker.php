<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Seo\Model\System\Template;


use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Model\Config;
use Mirasvit\Seo\Model\SeoObject\ProducturlFactory;


class Worker extends \Magento\Framework\DataObject
{
    protected $objectProducturlFactory;

    protected $productCollectionFactory;

    protected $config;

    /**
     * @var mixed
     */
    protected $catalogProductUrl;

    protected $storeManager;

    protected $scopeConfig;

    protected $dbResource;

    protected $context;

    /**
     * @var int
     */
    protected $maxPerStep = 500;

    /**
     * @var int
     */
    protected $totalNumber;

    /**
     * @var bool
     */
    protected $isEnterprise = false;

    public function __construct(
        ProducturlFactory $objectProducturlFactory,
        CollectionFactory $productCollectionFactory,
        Config $config,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResourceConnection $dbResource,
        Context $context,
        array $data = []
    ) {
        $this->objectProducturlFactory  = $objectProducturlFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config                   = $config;
        $this->storeManager             = $storeManager;
        $this->scopeConfig              = $scopeConfig;
        $this->dbResource               = $dbResource;
        $this->context                  = $context;

        parent::__construct($data);
    }

    public function run(): bool
    {
        $this->totalNumber = $this->getTotalProductNumber();
        if (($this->getStep() - 1) * $this->maxPerStep >= $this->totalNumber) {
            return false;
        }
        $this->process();

        return true;
    }

    protected function getTotalProductNumber(): int
    {
        $connection = $this->dbResource->getConnection('core_write');
        $select = $connection->select()->from($this->dbResource->getTableName('catalog_product_entity'));
        $select->reset(\Zend_Db_Select::ORDER);
        $select->reset(\Zend_Db_Select::LIMIT_COUNT);
        $select->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(\Zend_Db_Select::COLUMNS);
        $select->columns('COUNT(*)');
        $number = $connection->fetchOne($select);

        return (int)$number;
    }

    public function formatUrlKey(string $str): string
    {
        /** fixme $this->catalogProductUrl is null */
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $this->catalogProductUrl->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    public function getMaxPerStep(): int
    {
        return $this->maxPerStep;
    }

    public function getCurrentNumber(): int
    {
        $c = $this->getStep() * $this->getMaxPerStep();
        if ($c > $this->totalNumber) {
            return $this->totalNumber;
        } else {
            return $c;
        }
    }

    public function getTotalNumber(): int
    {
        return $this->totalNumber;
    }

    public function prepareUrlKeys(AdapterInterface $connection, string $urlKey, string $urlKeyTable): ?string
    {
        //for Magento Enterprise only
        if ($urlKey) {
            $selectAllStores = $connection->select()->from($urlKeyTable)->
                                    where('value LIKE ?', $urlKey.'%');
            $rowAllStores = $connection->fetchAll($selectAllStores);
            if ($rowAllStores) {
                $urlKeyValues = [];
                $addNewKey = false;
                foreach ($rowAllStores as $valueStores) {
                    if ($valueStores['value'] == $urlKey) {
                        $addNewKey = true;
                    }
                    $urlKeyValues[] = $valueStores['value'];
                }
                if ($addNewKey) {
                    //True if such url key exist. We can't add the same url key because value is UNIQUE
                    // for magento Enterprise.
                    $i = 1;
                    do {
                        $urlNewKey = $urlKey.'-'.$i;
                        ++$i;
                    } while (in_array($urlNewKey, $urlKeyValues));
                    $urlKey = $urlNewKey;

                    return $urlKey;
                }
            }
        }

        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function process(): void
    {
        $connection = $this->dbResource->getConnection('core_write');

        $select = $connection
            ->select()
            ->from($this->dbResource->getTableName('eav_entity_type'))
            ->where("entity_type_code = 'catalog_product'");
        $productTypeId = $connection->fetchOne($select);
        $select = $connection
            ->select()
            ->from($this->dbResource->getTableName('eav_attribute'))
            ->where("entity_type_id = $productTypeId AND (attribute_code = 'url_path')");
        $urlPathId = $connection->fetchOne($select);
        $select = $connection
            ->select()
            ->from($this->dbResource->getTableName('eav_attribute'))
            ->where("entity_type_id = $productTypeId AND (attribute_code = 'url_key')");
        $urlKeyId = $connection->fetchOne($select);

        $config = $this->config;
        $stores = $this->storeManager->getStores();
        $urlKeyTable = $this->dbResource->getTableName('catalog_product_entity_varchar');
        foreach ($stores as $store) {
            $products = $this->productCollectionFactory->create()
                        ->addAttributeToSelect('*')
                        ->setCurPage($this->getStep())
                        ->setPageSize($this->maxPerStep)
                        ->setStore($store);
            foreach ($products as $product) {
                $urlKeyTemplate = $config->getProductUrlKey((int)$store->getId());
                if ($this->isEnterprise) {
                    if (empty($urlKeyTemplate)) {
                        // if "Product URL Key Template" is empty we will create [product_name] template
                        $urlKeyTemplate = '[product_name]';
                    }
                }
                $storeId = $store->getId();
                $templ = $this->objectProducturlFactory->create()
                            ->setProduct($product)
                            ->setStore($store);
                $urlKey = $templ->parse($urlKeyTemplate);
                $urlKey = $this->formatUrlKey($urlKey);

                if ($product->getUrlKey() == $urlKey) {
                    continue;
                }

                $urlSuffix = $this->scopeConfig->getValue(
                    'catalog/seo/product_url_suffix',
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    $store
                );

                $select = $connection->select()->from($urlKeyTable)->
                            where("entity_type_id = $productTypeId AND attribute_id = $urlKeyId AND entity_id = {
                                $product->getId()
                            } AND store_id = {$storeId}");

                $row = $connection->fetchRow($select); //echo $select;die;
                if ($row) {
                    if ($this->isEnterprise) {
                        if ($urlKeyPrepared = $this->prepareUrlKeys($connection, $urlKey, $urlKeyTable)) {
                            $urlKey = $urlKeyPrepared;
                        }
                    }

                    $connection->update(
                        $urlKeyTable,
                        [
                        'value' => $urlKey],
                        "entity_type_id = $productTypeId AND attribute_id = $urlKeyId AND entity_id = {
                            $product->getId()
                        } AND store_id = {$storeId}"
                    );
                } else {
                    if (!$this->isEnterprise) {
                        $data = [
                            'entity_type_id' => $productTypeId,
                            'attribute_id' => $urlKeyId,
                            'entity_id' => $product->getId(),
                            'store_id' => $storeId,
                            'value' => $urlKey,
                        ];

                        $connection->insert($urlKeyTable, $data);
                    }
                }

                if (!$this->isEnterprise) {
                    $select = $connection->select()->from($urlKeyTable)->
                            where("entity_type_id = $productTypeId AND attribute_id = $urlPathId AND entity_id = {
                            $product->getId()
                            } AND store_id = {$storeId}");
                    $row = $connection->fetchRow($select);
                    if ($row) {
                        $connection->update(
                            $urlKeyTable,
                            [
                            'value' => $urlKey.$urlSuffix],
                            "entity_type_id = $productTypeId AND attribute_id = $urlPathId AND entity_id = {
                                $product->getId()
                                } AND store_id = {$storeId}"
                        );
                    } else {
                        $data = [
                            'entity_type_id' => $productTypeId,
                            'attribute_id' => $urlPathId,
                            'entity_id' => $product->getId(),
                            'store_id' => $storeId,
                            'value' => $urlKey.$urlSuffix,
                        ];
                        $connection->insert($urlKeyTable, $data);
                    }
                }
            }
        }
    }
}

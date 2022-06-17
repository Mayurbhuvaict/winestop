<?php

namespace Meetanshi\ShippingRates\Model\ResourceModel\Rate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Meetanshi\ShippingRates\Helper\Data;

class Collection extends AbstractCollection
{
    protected $scopeConfig;
    protected $helper;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        Data $helper,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    public function _construct()
    {
        $this->_init('Meetanshi\ShippingRates\Model\Rate', 'Meetanshi\ShippingRates\Model\ResourceModel\Rate');
    }

    public function addAddressFilters($request)
    {
        $this->addFieldToFilter('country', [
            [
                'like' => $request->getDestCountryId(),
            ],
            [
                'eq' => '0',
            ],
            [
                'eq' => '',
            ],
        ]);

        $this->addFieldToFilter('state', [
            [
                'like' => $request->getDestRegionId(),
            ],
            [
                'eq' => '0',
            ],
            [
                'eq' => '',
            ],
        ]);

        $this->addFieldToFilter('city', [
            [
                'like' => $request->getDestCity(),
            ],
            [
                'eq' => '',
            ],
        ]);
        $numericZip = $this->helper->allowNumericZip();
        $zipCode = $request->getDestPostcode();
        if ($numericZip) {
            if ($zipCode == '*') {
                $zipCode = '';
            }
            $zipCodes = $this->helper->getNumericZip($zipCode);
            $zipCodes['district'] = $zipCodes['district'] !== '' ? intval($zipCodes['district']) : -1;

            $this->getSelect()
                ->where('`num_zip_from` <= ? OR `zip_from` = ""', $zipCodes['district'])
                ->where('`num_zip_to` >= ? OR `zip_to` = ""', $zipCodes['district']);

            if (!empty($zipCodes['area'])) {
                $this->addFieldToFilter('zip_from', [
                    [['regexp' => '^' . $zipCodes['area'] . '[0-9]+'], ['eq' => '']],
                ]);
            }

            //to prefer rate with zip
            $this->setOrder('num_zip_from', 'DESC');
            $this->addOrder('num_zip_to', 'DESC');

        } else {
            $this->getSelect()->where("? LIKE zip_from OR zip_from = ''", $request->getDestPostcode());
        }
        return $this;
    }

    public function addMethodFilters($methodIds)
    {
        $this->addFieldToFilter('method_id', ['in' => $methodIds]);

        return $this;
    }

    public function addTotalsFilters($totals, $shippingType)
    {
        $this->addFieldToFilter('price_from', ['lteq' => $totals['not_free_price']]);
        $this->addFieldToFilter('price_to', ['gteq' => $totals['not_free_price']]);
        $this->addFieldToFilter('weight_from', ['lteq' => $totals['not_free_weight']]);
        $this->addFieldToFilter('weight_to', ['gteq' => $totals['not_free_weight']]);
        $this->addFieldToFilter('qty_from', ['lteq' => $totals['not_free_qty']]);
        $this->addFieldToFilter('qty_to', ['gteq' => $totals['not_free_qty']]);
        $this->addFieldToFilter('shipping_type', [
            [
                'eq' => $shippingType,
            ],
            [
                'eq' => '',
            ],
            [
                'eq' => '0',
            ],
        ]);
        return $this;
    }
}

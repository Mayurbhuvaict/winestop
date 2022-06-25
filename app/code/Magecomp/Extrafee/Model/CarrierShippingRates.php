<?php

namespace Magecomp\Extrafee\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Meetanshi\ShippingRates\Model\Rate;
use Meetanshi\ShippingRates\Model\Carrier\ShippingRates;
use Meetanshi\ShippingRates\Model\ResourceModel\Method\CollectionFactory;
use Psr\Log\LoggerInterface;

class CarrierShippingRates extends ShippingRates
{
    protected $_code = 'shippingrates';
    protected $rateErrorFactory;
    protected $rateResultFactory;
    protected $rateMethodFactory;
    protected $shippingratesTableFactory;
    protected $shippingrates;

    public function __construct(ScopeConfigInterface $scopeConfig, ErrorFactory $rateErrorFactory, LoggerInterface $logger, ResultFactory $rateResultFactory, MethodFactory $rateMethodFactory, CollectionFactory $shippingratesTableFactory, Rate $shippingrates, array $data = [])
    {
        $this->rateErrorFactory = $rateErrorFactory;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->shippingratesTableFactory = $shippingratesTableFactory;
        $this->shippingrates = $shippingrates;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $rateResultFactory, $rateMethodFactory, $shippingratesTableFactory, $shippingrates, $data);
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigData('active')) {
            return false;
        }

        $result = $this->rateResultFactory->create();
        $collection = $this->shippingratesTableFactory->create()
            ->addFieldToFilter('main_table.is_active', 1)
            ->addStoreFilter($request->getStoreId())
            ->addCustomerGroupFilter($this->getCustomerGroupId($request))
            ->setOrder('pos');

        $resource = $collection->getResource();
        $shippingCategory = $resource->getTable('shippingrate_category');
        $collection->getSelect()->join(['sc'=>$shippingCategory],'main_table.category_id=sc.id',['category_name','position'])->order('position ASC');
        
        $rates = $this->shippingrates->calculate($request, $collection);

        $count = 0;
        foreach ($collection as $col) {
            $method = $this->rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            if (isset($rates[$col->getId()]['cost'])) {
                $method->setMethod($this->_code . $col->getId());
                $methodTitle = __($col->getName());
                $methodTitle = str_replace('{day}', $rates[$col->getId()]['time'], $methodTitle);
                $method->setMethodTitle($methodTitle);

                $method->setCarrierTitle($col->getCategoryName());
                $method->setCost($rates[$col->getId()]['cost']);
                $method->setPrice($rates[$col->getId()]['cost']);
                $result->append($method);
                $count++;
            }
        }

        if (($count == 0) && ($this->getConfigData('showmethod') == 1)) {
            $error = $this->rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            return $error;
        }
        return $result;
    }

    public function getCustomerGroupId($request)
    {
        $allItems = $request->getAllItems();

        if (!$allItems) {
            return 0;
        }

        foreach ($allItems as $item) {
            return $item->getProduct()->getCustomerGroupId();
        }
    }

    public function getAllowedMethods()
    {
        $collection = $this->shippingratesTableFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->setOrder('pos');
        $arr = [];
        foreach ($collection as $method) {
            $methodCode = 'shippingrates' . $method->getMethodId();
            $arr[$methodCode] = $method->getName();
        }
        return $arr;
    }
}

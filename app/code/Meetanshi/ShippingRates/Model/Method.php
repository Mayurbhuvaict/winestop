<?php

namespace Meetanshi\ShippingRates\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Meetanshi\ShippingRates\Model\MethodFactory;
use Magento\Framework\Model\AbstractModel;

class Method extends AbstractModel
{
    protected $methodFactory;

    public function __construct(Context $context, Registry $registry, MethodFactory $methodFactory)
    {
        $this->methodFactory = $methodFactory;
        parent::__construct($context, $registry);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Meetanshi\ShippingRates\Model\ResourceModel\Method');
    }

    public function massChangeStatus($ids, $status)
    {
        foreach ($ids as $id) {
            $model = $this->methodFactory->create()->load($id);
            $model->setIsActive($status);
            $model->save();
        }
        return $this;
    }
}

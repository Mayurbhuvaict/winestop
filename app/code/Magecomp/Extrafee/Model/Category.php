<?php

namespace Magecomp\Extrafee\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magecomp\Extrafee\Model\CategoryFactory;
use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel
{
    protected $categoryFactory;

    public function __construct(Context $context, Registry $registry, CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $registry);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Magecomp\Extrafee\Model\ResourceModel\Category');
    }

    public function massChangeStatus($ids, $status)
    {
        foreach ($ids as $id) {
            $model = $this->categoryFactory->create()->load($id);
            $model->setIsActive($status);
            $model->save();
        }
        return $this;
    }
}

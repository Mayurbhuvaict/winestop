<?php
namespace Winestop\Testimonials\Model;

class Testimonials extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Winestop\Testimonials\Model\ResourceModel\Testimonials');
    }
}
?>
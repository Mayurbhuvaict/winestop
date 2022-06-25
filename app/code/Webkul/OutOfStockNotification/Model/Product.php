<?php
/**
 * Webkul Software
 *
 * @category    Webkul
 * @package     Webkul_OutOfStockNotification
 * @author      Webkul
 * @copyright   Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license     https://store.webkul.com/license.html
 */
namespace Webkul\OutOfStockNotification\Model;

use Webkul\OutOfStockNotification\Api\Data\ProductInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Product Model Class Of OutOfStockNotification
 */
class Product extends \Magento\Framework\Model\AbstractModel implements ProductInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';
   
    /**#@+
     * OutOfStockNotification Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * OutOfStockNotification cache tag.
     */
    const CACHE_TAG = 'OutOfStockNotification_Product';
    
    /**
     * @var string
     */
    protected $_cacheTag = 'OutOfStockNotification_Product';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'OutOfStockNotification_Product';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\OutOfStockNotification\Model\ResourceModel\Product::class);
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteGallery();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route OutOfStockNotification
     *
     * @return \Webkul\OutOfStockNotification\Model\Product
     */
    public function noRouteImages()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Approved'), self::STATUS_DISABLED => __('Disapproved')];
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}

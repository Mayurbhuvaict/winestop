<?php
/**
 * Class Collection
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop
 * @license  https://www.icreativetechnologies.com  Open Software License (OSL 3.0)
 * @link     https://www.icreativetechnologies.com
 */
namespace Winestop\Banner\Model\ResourceModel\Banner;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop
 * @license  https://www.icreativetechnologies.com  Open Software License (OSL 3.0)
 * @link     https://www.icreativetechnologies.com
 */
class Collection extends AbstractCollection
{
    /**
     * Banner Id
     *
     * @var string
     */
    protected $idFieldName = 'banner_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Winestop\Banner\Model\Banner::class,
            \Winestop\Banner\Model\ResourceModel\Banner::class
        );
    }
}

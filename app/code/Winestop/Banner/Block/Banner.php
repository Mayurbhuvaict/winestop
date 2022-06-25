<?php
/**
 * Class Banner
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop <magento@Winestop-technologies.com>
 * @license  https://www.Winestop-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.Winestop-technologies.com
 */
namespace Winestop\Banner\Block;

use Winestop\Banner\Api\Data\BannerInterface;
use Winestop\Banner\Model\ResourceModel\Banner\Collection as BannerCollection;
use \Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Banner
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop <magento@Winestop-technologies.com>
 * @license  https://www.Winestop-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.Winestop-technologies.com
 */
class Banner extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface
{

    /**
     * DataCollection
     *
     * @var \Magento\Framework\Data\Collection
     */
    protected $dataCollection;

    /**
     * BannerCollectionFactory
     *
     * @var \Winestop\Banner\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * Timezone
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * DateTime
     *
     * @var DateTime
     */
    protected $date;

    /**
     * BannerHelper
     *
     * @var \Winestop\Banner\Helper\Data
     */
    protected $bannerHelper;

    /**
     * Banner constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context context
     * @param DateTime $date date
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface timezoneInterface
     * @param \Winestop\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory bannerCollectionFactory
     * @param \Magento\Framework\Data\Collection $dataCollection dataCollection
     * @param \Winestop\Banner\Helper\Data $bannerHelper
     * @param array $data data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Winestop\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory,
        \Magento\Framework\Data\Collection $dataCollection,
        \Winestop\Banner\Helper\Data $bannerHelper,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->timezoneInterface = $timezoneInterface;
        $this->dataCollection = $dataCollection;
        $this->date = $date;
        $this->bannerHelper = $bannerHelper;
    }

    /**
     * Retrieve config value
     *
     * @return string
     */
    public function getConfig($config)
    {
        return $this->bannerHelper->getConfig($config);
    }

    /**
     * Return Banner Collection
     *
     * @return \Winestop\Banner\Model\ResourceModel\Banner\Collection
     * @throws \Exception
     */
    public function getBanner()
    {
        if (!$this->hasData('banner')) {
            $date = $this->timezoneInterface->date()->format('Y-m-d H:i:s');
            $banner = $this->bannerCollectionFactory
                ->create()
                ->addFilter('is_active', 1)
                ->addOrder(
                    BannerInterface::POSITION,
                    BannerCollection::SORT_ORDER_ASC
                );
                $collection = $this->dataCollection;
            foreach ($banner as $value) {
                $data = $value->getData();
                if ((($data['start_date'] <= $date) || ($data['start_date'] == null))
                    && (($data['end_date'] >= $date) || ($data['end_date'] == null))
                ) {
                    $rowObj = new \Magento\Framework\DataObject();
                    $rowObj->setData($data);
                    $collection->addItem($rowObj);
                }
            }
            $this->setData('banner', $collection);
        }
        return $this->getData('banner');
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Winestop\Banner\Model\Banner::CACHE_TAG . '_' . 'list'];
    }

    /**
     * Return Media Path
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaPath()
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}

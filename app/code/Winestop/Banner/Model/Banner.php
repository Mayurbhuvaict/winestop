<?php
/**
 * Class Banner
 *
 * PHP version 7
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop
 * @license  https://www.icreativetechnologies.com  Open Software License (OSL 3.0)
 * @link     https://www.icreativetechnologies.com
 */
namespace Winestop\Banner\Model;

use Winestop\Banner\Api\Data\BannerInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Banner
 *
 * @category Winestop
 * @package  Winestop_Banner
 * @author   Winestop
 * @license  https://www.icreativetechnologies.com  Open Software License (OSL 3.0)
 * @link     https://www.icreativetechnologies.com
 */
class Banner extends AbstractModel implements BannerInterface, IdentityInterface
{
    /**
     * Base media path for banner's image
     */
    const BASE_IMAGE_MEDIA_PATH = 'Winestop/banner/image';

    /**
     * Base media path for banner's image
     */
    const BASE_VIDEO_MEDIA_PATH = 'Winestop/banner/video';

    /**
     * Banner's Status enabled
     */
    const STATUS_ENABLED = 1;

    /**
     *Banner's Status disabled
     */
    const STATUS_DISABLED = 0;

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'Winestop_banner';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag = 'Winestop_banner';

    /**
     * Prefix of model banner names
     *
     * @var string
     */
    protected $eventPrefix = 'Winestop_banner';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Winestop\Banner\Model\ResourceModel\Banner::class);
    }

    /**
     * Retrive Model Title
     *
     * @param boolean $plural plural
     *
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Banners' : 'Banner';
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get banner ID
     *
     * @return int|null
     */
    public function getBannerId()
    {
        return $this->getData(self::BANNER_ID);
    }

    /**
     * Get banner title
     *
     * @return string|null
     */
    public function getBannerTitle()
    {
        return $this->getData(self::BANNER_TITLE);
    }

    /**
     * Get banner description
     *
     * @return string|null
     */
    public function getBannerDescription()
    {
        return $this->getData(self::BANNER_DESCRIPTION);
    }

    /**
     * Get banner type
     *
     * @return string|null
     */
    public function getBannerType()
    {
        return $this->getData(self::BANNER_TYPE);
    }

    /**
     * Get banner image
     *
     * @return string|null
     */
    public function getBannerImage()
    {

        return $this->getData(self::BANNER_IMAGE);
    }

    /**
     * Get banner video
     *
     * @return string|null
     */
    public function getBannerVideo()
    {

        return $this->getData(self::BANNER_VIDEO);
    }

    /**
     * Get banner youtube
     *
     * @return string|null
     */
    public function getBannerYoutube()
    {

        return $this->getData(self::BANNER_YOUTUBE);
    }

    /**
     * Get banner video autoplay
     *
     * @return string|null
     */
    public function getBannerVideoAutoplay()
    {

        return $this->getData(self::BANNER_VIDEO_AUTOPLAY);
    }

    /**
     * Retrieve true if banner is active
     *
     * @return boolean [description]
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Get button text for action of banner
     *
     * @return string|null
     */
    public function getLabelButtonText()
    {
        return $this->getData(self::LABEL_BUTTON_TEXT);
    }

    /**
     * Get action of banner button
     *
     * @return string|null
     */
    public function getCallToAction()
    {
        return $this->getData(self::CALL_TO_ACTION);
    }

    /**
     * Get banner position
     *
     * @return int|null
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * Get start date
     *
     * @return string|null
     */
    public function getStartDate()
    {
        return $this->getData(self::START_DATE);
    }

    /**
     * Get end date
     *
     * @return string|null
     */
    public function getEndDate()
    {
        return $this->getData(self::END_DATE);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Set banner id
     *
     * @param int $bannerId bannerId
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerId($bannerId)
    {
        return $this->setData(self::BANNER_ID, $bannerId);
    }

    /**
     * Set banner title
     *
     * @param string $bannerTitle bannerTitle
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerTitle($bannerTitle)
    {
        return $this->setData(self::BANNER_TITLE, $bannerTitle);
    }

    /**
     * Set banner description
     *
     * @param string $bannerDescription bannerDescription
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerDescription($bannerDescription)
    {
        return $this->setData(self::BANNER_DESCRIPTION, $bannerDescription);
    }

    /**
     * Set banner type
     *
     * @param string $bannerType bannerType
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerType($bannerType)
    {
        return $this->setData(self::BANNER_TYPE, $bannerType);
    }

    /**
     * Set banner image
     *
     * @param string $bannerImage bannerImage
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerImage($bannerImage)
    {
        return $this->setData(self::BANNER_IMAGE, $bannerImage);
    }

    /**
     * Set banner video
     *
     * @param string $bannerVideo bannerVideo
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerVideo($bannerVideo)
    {
        return $this->setData(self::BANNER_VIDEO, $bannerVideo);
    }

    /**
     * Set banner youtube
     *
     * @param string $bannerYoutube bannerYoutube
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerYoutube($bannerYoutube)
    {
        return $this->setData(self::BANNER_YOUTUBE, $bannerYoutube);
    }

    /**
     * Set banner video autoplay
     *
     * @param string $bannerVideoAutoplay bannerVideoAutoplay
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setBannerVideoAutoplay($bannerVideoAutoplay)
    {
        return $this->setData(self::BANNER_VIDEO_AUTOPLAY, $bannerVideoAutoplay);
    }

    /**
     * Set button text
     *
     * @param string $labelbuttonText labelbuttonText
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setLabelButtonText($labelbuttonText)
    {
        return $this->setData(self::LABEL_BUTTON_TEXT, $labelbuttonText);
    }

    /**
     * Set  calltoaction
     *
     * @param string $calltoAction calltoAction
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setCallToAction($calltoAction)
    {
        return $this->setData(self::CALL_TO_ACTION, $calltoAction);
    }

    /**
     * Set banner position
     *
     * @param int|null $position position
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Set start date
     *
     * @param string $start_date start_date
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setStartDate($start_date)
    {
        return $this->setData(self::START_DATE, $start_date);
    }

    /**
     * Set end date
     *
     * @param string $end_date end_date
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setEndDate($end_date)
    {
        return $this->setData(self::END_DATE, $end_date);
    }

    /**
     * Set event creation time
     *
     * @param string $creation_time creation_time
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setCreationTime($creation_time)
    {
        return $this->setData(self::CREATION_TIME, $creation_time);
    }

    /**
     * Set update time
     *
     * @param string $update_time update_time
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setUpdateTime($update_time)
    {
        return $this->setData(self::UPDATE_TIME, $update_time);
    }

    /**
     * Set is active
     *
     * @param int|bool $is_active is_active
     *
     * @return \Winestop\Banner\Api\Data\BannerInterface
     */
    public function setIsActive($is_active)
    {
        return $this->setData(self::IS_ACTIVE, $is_active);
    }
}

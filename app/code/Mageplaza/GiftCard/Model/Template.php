<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Mageplaza\GiftCard\Api\Data\GiftTemplateInterface;

/**
 * Class Template
 * @package Mageplaza\GiftCard\Model
 */
class Template extends AbstractModel implements IdentityInterface, GiftTemplateInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'mageplaza_giftcard_template';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_giftcard_template';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Template::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTemplateId($value)
    {
        return $this->setData(self::TEMPLATE_ID, $value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @return string
     */
    public function getCanUpload()
    {
        return $this->getData(self::CAN_UPLOAD);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCanUpload($value)
    {
        return $this->setData(self::CAN_UPLOAD, $value);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    /**
     * @return string
     */
    public function getFontFamily()
    {
        return $this->getData(self::FONT_FAMILY);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFontFamily($value)
    {
        return $this->setData(self::FONT_FAMILY, $value);
    }

    /**
     * @return string
     */
    public function getBackgroundImage()
    {
        return $this->getData(self::BACKGROUND_IMAGE);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBackgroundImage($value)
    {
        return $this->setData(self::BACKGROUND_IMAGE, $value);
    }

    /**
     * @return string
     */
    public function getDesign()
    {
        return $this->getData(self::DESIGN);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDesign($value)
    {
        return $this->setData(self::DESIGN, $value);
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->getData(self::NOTE);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNote($value)
    {
        return $this->setData(self::NOTE, $value);
    }

    /**
     * @return string
     */
    public function getImages()
    {
        return $this->getData(self::IMAGES);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImages($value)
    {
        return $this->setData(self::IMAGES, $value);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }
}

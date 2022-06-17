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

namespace Mageplaza\GiftCard\Api\Data;

/**
 * Interface GiftTemplateInterface
 * @api
 */
interface GiftTemplateInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const TEMPLATE_ID      = 'template_id';
    const NAME             = 'name'; // yes
    const STATUS           = 'status'; // yes
    const CAN_UPLOAD       = 'can_upload'; // yes
    const TITLE            = 'title'; // yes
    const FONT_FAMILY      = 'font_family'; // yes
    const BACKGROUND_IMAGE = 'background_image'; // maybe
    const DESIGN           = 'design'; // maybe
    const NOTE             = 'note'; // yes
    const IMAGES           = 'images'; // maybe
    const CREATED_AT       = 'created_at';

    /**
     * @return string
     */
    public function getTemplateId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTemplateId($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getCanUpload();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCanUpload($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTitle($value);

    /**
     * @return string
     */
    public function getFontFamily();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFontFamily($value);

    /**
     * @return string
     */
    public function getBackgroundImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBackgroundImage($value);

    /**
     * @return string
     */
    public function getDesign();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDesign($value);

    /**
     * @return string
     */
    public function getNote();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNote($value);

    /**
     * @return string
     */
    public function getImages();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImages($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);
}

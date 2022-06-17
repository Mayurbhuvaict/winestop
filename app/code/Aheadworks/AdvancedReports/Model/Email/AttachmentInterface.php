<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */


































namespace Aheadworks\AdvancedReports\Model\Email;

/**
 * Interface AttachmentInterface
 *
 * @package Aheadworks\AdvancedReports\Model\Email
 */
interface AttachmentInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ATTACHMENT = 'attachment';
    const FILE_NAME = 'file_name';
    /**#@-*/

    /**
     * Get attachment
     *
     * @return array|string
     */
    public function getAttachment();

    /**
     * Set attachment
     *
     * @param array|string $attachment
     * @return $this
     */
    public function setAttachment($attachment);

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);
}

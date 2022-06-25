<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoContent\Api\Data;

interface RewriteInterface extends ContentInterface
{
    const TABLE_NAME = 'mst_seo_content_rewrite';

    const ID = 'rewrite_id';
    const URL = 'url';
    const IS_ACTIVE = 'is_active';
    const SORT_ORDER = 'sort_order';
    const STORE_IDS = 'store_ids';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $value
     * @return $this
     */
    public function setUrl($value);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param string $value
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return string
     */
    public function getSortOrder();

    /**
     * @param array $value
     * @return $this
     */
    public function setStoreIds(array $value);

    /**
     * @return array
     */
    public function getStoreIds();
}

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



namespace Mirasvit\SeoContent\Api\Repository;

use Mirasvit\SeoContent\Api\Data\RewriteInterface;

interface RewriteRepositoryInterface
{
    /**
     * @return RewriteInterface[]|\Mirasvit\SeoContent\Model\ResourceModel\Rewrite\Collection
     */
    public function getCollection();

    /**
     * @return RewriteInterface
     */
    public function create();

    /**
     * @param int $id
     * @return RewriteInterface|false
     */
    public function get($id);

    /**
     * @param RewriteInterface $rewrite
     * @return RewriteInterface
     */
    public function save(RewriteInterface $rewrite);

    /**
     * @param RewriteInterface $rewrite
     * @return bool
     */
    public function delete(RewriteInterface $rewrite);
}

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


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;

class Url extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(UrlInterface::TABLE_NAME, UrlInterface::ID);
    }
}

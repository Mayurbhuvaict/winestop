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



namespace Mirasvit\SeoSitemap\Model\Pager;

use Mirasvit\SeoSitemap\Model\Config;
use Magento\Framework\Model\Context;
use Magento\Framework\DataObject;

class Collection extends DataObject
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var int
     */
    protected $pageSize;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var array
     */
    protected $collection = [];

    /**
     * @param Config  $config
     * @param Context $context
     */
    public function __construct(
        Config $config,
        Context $context
    ) {
        $this->config  = $config;
        $this->context = $context;

        parent::__construct();
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getLastPageNumber()
    {
        return ceil(count($this->collection) / $this->getSize());
    }

    /**
     * @param int $displacement
     * @return int
     */
    public function getCurPage($displacement = 0)
    {
        if ($displacement == 0) {
            return $this->currentPage;
        }
        if ($this->currentPage + $displacement < 1) {
            return 1;
        } elseif ($this->currentPage + $displacement > $this->getLastPageNumber()) {
            return $this->getLastPageNumber();
        } else {
            return $this->currentPage + $displacement;
        }
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setPageSize($size)
    {
        $this->pageSize = $size;

        return $this;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setCurPage($page)
    {
        $this->currentPage = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * @param array $collection
     *
     * @return void
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function getCollection()
    {
        $collection = $this->collection;
        if ($this->getSize() > 0) {
            $limit      = $this->getSize();
            $offset     = ($this->getCurPage() - 1) * $limit;
            $collection = array_slice($this->collection, $offset, $limit);
        }

        return $collection;
    }
}

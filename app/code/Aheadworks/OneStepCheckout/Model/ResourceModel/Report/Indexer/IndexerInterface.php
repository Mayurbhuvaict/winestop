<?php
namespace Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Indexer;

/**
 * Interface IndexerInterface
 * @package Aheadworks\OneStepCheckout\Model\ResourceModel\Report\Indexer
 */
interface IndexerInterface
{
    /**
     * Reindex all
     *
     * @return $this
     */
    public function reindexAll();
}

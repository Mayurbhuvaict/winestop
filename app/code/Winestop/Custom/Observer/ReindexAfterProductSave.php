<?php

namespace Winestop\Custom\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Mview;
use Magento\Framework\Indexer\StateInterface;

/**
 * Class ReindexAfterProductSave
 * 
 * Reindex after product save
 */
class ReindexAfterProductSave implements ObserverInterface
{
    /**
     * @var IndexerFactory
     */
    protected $indexFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    protected $indexerRegistry;

    /**
     * @param IndexerFactory $indexFactory
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerFactory $indexFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $collectionFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->indexFactory = $indexFactory;
        $this->collectionFactory = $collectionFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->logger = $logger;
    }

    /**
     * Trigger event
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /*$indexIds = [];
        $product = $observer->getEvent()->getProduct();
        try {
            if($product->getId()){
                $id = $product->getId();
                $catalogSearchProcessor = $this->indexerRegistry->get(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID);
                $catalogSearchProcessor->reindexRow($id);

                $productCategoryIndexer = $this->indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID);
                $productCategoryIndexer->reindexRow($id);

                $categoryProductIndexer = $this->indexerRegistry->get(\Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID);
                $categoryProductIndexer->reindexRow($id);

                $eavProcessor = $this->indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Eav\Processor::INDEXER_ID);
                $eavProcessor->reindexRow($id);

                $priceProcessor = $this->indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Price\Processor::INDEXER_ID);
                $priceProcessor->reindexRow($id);

                $productFlatProcessor = $this->indexerRegistry->get(\Magento\Catalog\Model\Indexer\Product\Flat\Processor::INDEXER_ID);
                $productFlatProcessor->reindexRow($id);
                
                $stockProcessor = $this->indexerRegistry->get(\Magento\CatalogInventory\Model\Indexer\Stock\Processor::INDEXER_ID);
                $stockProcessor->reindexRow($id);

                $catalogRuleProcessor = $this->indexerRegistry->get(\Magento\CatalogRule\Model\Indexer\Rule\RuleProductProcessor::INDEXER_ID);
                $catalogRuleProcessor->reindexRow($id);
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }*/
    }

    /**
     * Get Pending Indexer Count
     * 
     * @return int
     */
    public function getPendingCount(Mview\ViewInterface $view)
    {
        $changelog = $view->getChangelog();

        try {
            $currentVersionId = $changelog->getVersion();
        } catch (Mview\View\ChangelogTableNotExistsException $e) {
            return '';
        }

        $state = $view->getState();
        $pendingCount = count($changelog->getList($state->getVersionId(), $currentVersionId));

        return $pendingCount;
    }
}
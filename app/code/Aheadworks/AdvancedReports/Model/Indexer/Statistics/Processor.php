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
























































































































































































namespace Aheadworks\AdvancedReports\Model\Indexer\Statistics;

use Magento\Framework\Indexer\StateInterface;

/**
 * Class Processor
 *
 * @package Aheadworks\AdvancedReports\Model\Indexer\Statistics
 */
class Processor extends \Magento\Framework\Indexer\AbstractProcessor
{
    const INDEXER_ID = 'aw_arep_statistics';

    /**
     * Is reindex scheduled
     *
     * @return bool
     */
    public function isReindexScheduled()
    {
        /** @var StateInterface $state */
        $state = $this->getIndexer()->getState();
        if ($state->getStatus() == StateInterface::STATUS_INVALID) {
            return true;
        }
        return false;
    }
}

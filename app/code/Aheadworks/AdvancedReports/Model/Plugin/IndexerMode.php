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










































































































































































namespace Aheadworks\AdvancedReports\Model\Plugin;

use Aheadworks\AdvancedReports\Model\Indexer\Statistics\Processor as AdvancedReportsIndexer;
use Magento\Framework\Mview\View\StateInterface;

/**
 * Class IndexerMode
 *
 * @package Aheadworks\AdvancedReports\Model\Plugin
 */
class IndexerMode
{
    /**
     * Disable UPDATE ON SAVE mode
     *
     * @param \Magento\Indexer\Model\Mview\View\State\Interceptor $mode
     * @return \Magento\Indexer\Model\Mview\View\State\Interceptor
     */
    public function afterSetMode(
        \Magento\Indexer\Model\Mview\View\State\Interceptor $mode
    ) {
        if ($mode->getViewId() == AdvancedReportsIndexer::INDEXER_ID
            && StateInterface::MODE_DISABLED == $mode->getMode()
        ) {
            $mode->setMode(StateInterface::MODE_ENABLED);
        }
        return $mode;
    }
}

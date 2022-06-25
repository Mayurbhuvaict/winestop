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


namespace Mirasvit\SeoAudit\Model\ResourceModel\CheckResult;


use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\SelectFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Model\ResourceModel\CheckResult;
use Psr\Log\LoggerInterface as Logger;

class Grid extends SearchResult
{
    private $context;

    public function __construct(
        ContextInterface $context,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        string $mainTable = null,
        string $resourceModel = null
    ) {
        $mainTable     = CheckResultInterface::TABLE_NAME;
        $resourceModel = CheckResult::class;

        $this->context = $context;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    protected function _initSelect()
    {
        $jobId = $this->context->getRequestParam(JobInterface::ID);

        $t2 = '(SELECT count(url_id) AS url_count, job_id, identifier FROM '
            . $this->getTable(CheckResultInterface::TABLE_NAME)
            . ' WHERE result < 0 GROUP BY job_id, identifier)';

        $t3 = '(SELECT count(url_id) AS url_count, job_id, identifier FROM '
            . $this->getTable(CheckResultInterface::TABLE_NAME)
            . ' WHERE result >= 0 AND result < 5 GROUP BY job_id, identifier)';

        $t4 = '(SELECT count(url_id) AS url_count, job_id, identifier FROM '
            . $this->getTable(CheckResultInterface::TABLE_NAME)
            . ' WHERE result >= 5 AND result < 9 GROUP BY job_id, identifier)';

        $this->_select
            ->from(
                ['main' => $this->_resource->getTable(CheckResultInterface::TABLE_NAME)],
                null
            )->columns([
                'identifier' => 'main.identifier',
                'job_id'     => 'main.job_id',
                'total'      => 'COUNT(main.url_id)',
                'error'      => 'IFNULL(t2.url_count, 0)',
                'warning'    => 'IFNULL(t3.url_count, 0)',
                'notice'     => 'IFNULL(t4.url_count, 0)'
            ])->joinLeft(
                ['t2' => new \Zend_Db_Expr($t2)],
                'main.identifier = t2.identifier AND main.job_id = t2.job_id',
                null
            )->joinLeft(
                ['t3' => new \Zend_Db_Expr($t3)],
                'main.identifier = t3.identifier AND main.job_id = t3.job_id',
                null
            )->joinLeft(
                ['t4' => new \Zend_Db_Expr($t4)],
                'main.identifier = t4.identifier AND main.job_id = t4.job_id',
                null
            )
            ->group(['main.job_id', 'main.identifier']);

        if ($jobId) {
            $this->_select->where('main.job_id = ' . $jobId);
        }

        return $this;
    }
}

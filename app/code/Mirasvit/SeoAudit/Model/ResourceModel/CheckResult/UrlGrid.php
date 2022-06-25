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
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Api\Data\UrlInterface;
use Mirasvit\SeoAudit\Model\ResourceModel\CheckResult;
use Psr\Log\LoggerInterface as Logger;

class UrlGrid extends SearchResult
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
        $this->_select->from(
            ['main_table' => $this->_resource->getTable(CheckResultInterface::TABLE_NAME)]
        )->columns(
            ['checks' => 'GROUP_CONCAT(CONCAT_WS("|", identifier, result))', 'result' => 'GROUP_CONCAT(result)']
        )->joinInner(
            ['url_table' => $this->_resource->getTable(UrlInterface::TABLE_NAME)],
            'main_table.url_id = url_table.url_id',
            ['url_table.url', 'url_table.meta_title', 'url_table.meta_description', 'url_table.status_code', 'url_table.parent_ids']
        )->group('main_table.url_id');

        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, [CheckResultInterface::JOB_ID, CheckResultInterface::IDENTIFIER, CheckResultInterface::RESULT])) {
            $field = 'main_table.' . $field;
        }

        if ($field == 'main_table.' . CheckResultInterface::RESULT) {
            $value = $condition['eq'];
            $range = [];

            switch ($value) {
                case 'error':
                    $range  = range(-10, -1);

                    break;
                case 'warning':
                    $range = range(0, 5);

                    break;
                case 'notice':
                    $range = range(6, 9);

                    break;
            }

            foreach ($range as $item) {
                $condition[] = ['finset' => $item];
                $fields[]    = $field;

            }
            $field = $fields;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}

<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Block\Adminhtml\Customer\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Block\Adminhtml\Customer\Edit\Tab\Transaction\DetailRenderer;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\ResourceModel\Transaction\CollectionFactory;
use Mageplaza\GiftCard\Model\Transaction\Action;

/**
 * Class Transaction
 * @package Mageplaza\GiftCard\Block\Adminhtml\Customer\Edit\Tab
 */
class Transaction extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var DataHelper
     */
    protected $_helper;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Registry $registry,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry      = $registry;
        $this->_helper            = $dataHelper;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('transaction_grid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * Get Customer Id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->getSelect()
            ->join(
                ['cr' => $collection->getTable('mageplaza_giftcard_credit')],
                'main_table.credit_id = cr.credit_id AND cr.customer_id = ' . $this->getCustomerId(),
                ['customer_id']
            );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended|Transaction
     * @throws Exception
     * @throws NoSuchEntityException
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
            'header'           => __('#'),
            'index'            => 'transaction_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('created_at', [
            'header'           => __('Date'),
            'type'             => 'datetime',
            'index'            => 'created_at',
            'header_css_class' => 'col-date',
            'column_css_class' => 'col-date'
        ]);

        $this->addColumn('action', [
            'header'  => __('Action'),
            'index'   => 'action',
            'type'    => 'options',
            'options' => Action::getOptionArray()
        ]);

        $customer = $this->_helper->getCustomer($this->getCustomerId());

        $this->addColumn('balance', [
            'header'        => __('Balance'),
            'filter'        => false,
            'align'         => 'right',
            'index'         => 'balance',
            'type'          => 'price',
            'currency_code' => $customer->getStore()->getBaseCurrencyCode()
        ]);

        $this->addColumn('amount', [
            'header'        => __('Amount Change'),
            'filter'        => false,
            'align'         => 'right',
            'index'         => 'amount',
            'type'          => 'price',
            'currency_code' => $customer->getStore()->getBaseCurrencyCode()
        ]);

        $this->addColumn('detail', [
            'header'   => __('Detail'),
            'filter'   => false,
            'sortable' => false,
            'renderer' => DetailRenderer::class,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('mpgiftcard/customer/grid', ['_current' => true]);
    }
}

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

use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\GiftCard\Helper\Data as DataHelper;

/**
 * Class Balance
 *
 * @package Mageplaza\GiftCard\Block\Adminhtml\Customer\Edit\Tab
 */
class Balance extends Template
{
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
     * @var float Customer balance
     */
    protected $_balance;

    /**
     * Balance constructor.
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        Registry $registry,
        array $data = []
    ) {
        $this->_helper       = $dataHelper;
        $this->_coreRegistry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return float
     * @throws NoSuchEntityException
     */
    public function getBalanceAmountFormatted()
    {
        $customer = $this->_helper->getCustomer($this->getCustomerId());

        return $this->_helper->formatPrice(
            $this->getBalanceAmount(),
            true,
            null,
            $customer->getStore()->getBaseCurrency()
        );
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
     * Get Balance amount
     *
     * @return float|int
     * @throws NoSuchEntityException
     */
    public function getBalanceAmount()
    {
        if (!$this->_balance) {
            $this->_balance = $this->_helper->getCustomerBalance($this->getCustomerId(), false) ?: 0;
        }

        return $this->_balance;
    }

    /**
     * Get change amount url
     *
     * @return string
     */
    public function getChangeAmountUrl()
    {
        return $this->_urlBuilder->getUrl('mpgiftcard/customer/change', ['isAjax' => true]);
    }
}

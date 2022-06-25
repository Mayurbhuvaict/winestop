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

namespace Mageplaza\GiftCard\Controller\Adminhtml\Pool;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManager;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Helper\Data as DataHelper;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\TransactionFactory;

/**
 * Class Save
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class Save extends Pool
{
    /**
     * @var GiftCardFactory
     */
    protected $_giftCardFactory;

    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var DataHelper
     */
    protected $_dataHelper;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param GiftCardFactory $giftCardFactory
     * @param TransactionFactory $transactionFactory
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        GiftCardFactory $giftCardFactory,
        TransactionFactory $transactionFactory,
        DataHelper $dataHelper
    ) {
        $this->_giftCardFactory    = $giftCardFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_dataHelper         = $dataHelper;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * @return void
     * @var PageFactory
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            // init model and set data
            $pool = $this->_initObject();

            $this->prepareData($data);
            if (!empty($data)) {
                $pool->addData($data);
            }

            // try to save it
            try {
                $pool->save();

                $this->messageManager->addSuccessMessage(__('The Gift Code Pool has been saved successfully.'));

                // clear previously saved data from session
                $this->_getSession()->setData('pool_form_data', false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $pool->getId()]);

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (LocalizedException $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving gift code pool.' . $e->getMessage())
                );
            }

            $this->_getSession()->setData('pool_form_data', $data);

            // redirect to edit form
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function prepareData(&$data)
    {
        if (isset($data['template_fields'])) {
            $data['template_fields'] = DataHelper::jsonEncode($data['template_fields']);
        }
        $data['action_vars'] = ['auth' => $this->_auth->getUser()->getName()];

        $store = $this->_objectManager->get(StoreManager::class);
        if ($store->isSingleStoreMode()) {
            $data['store_id'] = $store->getStore(true)->getId();
        }
        if (isset($data['rule'])) {
            $data['conditions_serialized'] = $this->_dataHelper->convertConditions($data['rule']);
        }

        return $this;
    }
}

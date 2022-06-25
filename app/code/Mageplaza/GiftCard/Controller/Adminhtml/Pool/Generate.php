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
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\PoolFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
use Psr\Log\LoggerInterface;
use Zend_Db_Select;

/**
 * Class Generate
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Pool
 */
class Generate extends Pool
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Generate constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param CollectionFactory $collectionFactory
     * @param GiftCardFactory $cardFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        CollectionFactory $collectionFactory,
        GiftCardFactory $cardFactory,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->giftCardFactory   = $cardFactory;
        $this->logger            = $logger;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * Generate
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');

            return;
        }

        $result = [];
        $pool   = $this->_initObject();
        if ($pool && $pool->getId()) {
            try {
                if ($giftCards = $this->generate($pool)) {
                    $this->messageManager->addSuccessMessage(__('%1 code(s) have been created.', count($giftCards)));

                    $result['success'] = true;
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while creating gift cards. Please review the log and try again.'));
                $this->logger->critical($e);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Pool is not defined'));
        }

        $this->_view->getLayout()->initMessages();
        $result['messages'] = $this->_view->getLayout()->getMessagesBlock()->getGroupedHtml();

        $this->getResponse()->representJson(Data::jsonEncode($result));
    }

    /**
     * @param \Mageplaza\GiftCard\Model\Pool $pool
     *
     * @return array
     * @throws InputException
     * @throws LocalizedException
     */
    public function generate($pool)
    {
        $data = $this->getRequest()->getParams();

        if (empty($data['pattern']) || empty($data['qty'])) {
            throw new InputException(__('Invalid date provided'));
        }

        $giftCard = $this->giftCardFactory->create()
            ->setData($pool->getData())
            ->addData([
                'pattern'       => $data['pattern'],
                'pool_id'       => $pool->getId(),
                'extra_content' => Data::jsonEncode(['auth' => $this->_auth->getUser()->getName()]),
                'action_vars'   => Data::jsonEncode(['pool_id' => $pool->getId()])
            ]);

        return $giftCard->createMultiple(['qty' => $data['qty']]);
    }

    /**
     * @param \Mageplaza\GiftCard\Model\Pool $pool
     * @param array $codes
     *
     * @return array
     * @throws LocalizedException
     */
    protected function generateByCodes($pool, $codes)
    {
        $codes = array_filter($codes);

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('code', ['in' => $codes])
            ->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('code');

        if ($existed = $collection->getColumnValues('code')) {
            $this->messageManager->addErrorMessage(__('Duplicated gift code(s) "%1"', implode(', ', $existed)));

            $codes = array_diff($codes, $existed);
        }

        if (empty($codes)) {
            return [];
        }

        $giftCard = $this->giftCardFactory->create()
            ->setData($pool->getData())
            ->addData([
                'pool_id'       => $pool->getId(),
                'extra_content' => Data::jsonEncode(['auth' => $this->_auth->getUser()->getName()]),
                'action_vars'   => Data::jsonEncode(['pool_id' => $pool->getId()])
            ]);

        return $giftCard->createMultiple(['codes' => $codes]);
    }
}

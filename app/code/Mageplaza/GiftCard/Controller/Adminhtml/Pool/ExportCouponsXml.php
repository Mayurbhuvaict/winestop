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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Block\Adminhtml\Pool\Edit\Tab\Generate\Grid;
use Mageplaza\GiftCard\Controller\Adminhtml\Pool;
use Mageplaza\GiftCard\Model\PoolFactory;

/**
 * Class ExportCouponsXml
 * @package Mageplaza\GiftCard\Controller\Adminhtml\Template
 */
class ExportCouponsXml extends Pool
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * Constructor.
     *
     * @param Registry $registry
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PoolFactory $poolFactory
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Registry $registry,
        Context $context,
        PageFactory $resultPageFactory,
        PoolFactory $poolFactory,
        FileFactory $fileFactory
    ) {
        $this->_coreRegistry = $registry;
        $this->_fileFactory  = $fileFactory;

        parent::__construct($context, $resultPageFactory, $poolFactory);
    }

    /**
     * Export coupon codes as excel xml file
     *
     * @return ResponseInterface|ResultInterface|null
     * @throws Exception
     */
    public function execute()
    {
        $pool = $this->_initObject();

        if ($pool->getId()) {
            $fileName = "pool_{$pool->getId()}.xml";
            $this->_coreRegistry->register('current_pool', $pool);

            $content = $this->_view->getLayout()
                ->createBlock(Grid::class)
                ->getExcelFile($fileName);

            return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
        }

        return null;
    }
}

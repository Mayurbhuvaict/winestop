<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Adminhtml;

/**
 * Simple google shopping backend controller
 */
abstract class Feeds extends \Magento\Backend\App\Action
{
    /**
     * Authorization level
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_SimpleGoogleShopping::main';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \Wyomind\Framework\Helper\Heartbeat
     */
    public $heartbeatHelper = null;

    /**
     * @var \Wyomind\Framework\Helper\License
     */
    public $licenseHelper = null;

    /**
     * @var \Wyomind\SimpleGoogleShopping\Helper\Data
     */
    public $sgsHelper = null;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    public $resultForwardFactory = null;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultRawFactory = null;

    /**
     * @var \Wyomind\SimpleGoogleShopping\Model\Feeds
     */
    public $sgsModel = null;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    public $attributeRepository = null;

    /**
     * @var \Wyomind\SimpleGoogleShopping\Helper\Parser
     */
    public $parserHelper = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory = null;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory
     */
    protected $feedsCollectionFactory;

    public $resultRedirectFactory = null;
    public $directoryRead = null;
    public $cacheManager = null;

    /**
     * Feeds constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Model\Context $contextModel
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Wyomind\Framework\Helper\Heartbeat $heartbeatHelper
     * @param \Wyomind\Framework\Helper\License $licenseHelper
     * @param \Wyomind\SimpleGoogleShopping\Helper\Data $sgsHelper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $sgsModel
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \Wyomind\SimpleGoogleShopping\Helper\Parser $parserHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead
     * @param \Magento\Framework\Module\Dir\Reader $directoryReader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Model\Context $contextModel,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Wyomind\Framework\Helper\Heartbeat $heartbeatHelper,
        \Wyomind\Framework\Helper\License $licenseHelper,
        \Wyomind\SimpleGoogleShopping\Helper\Data $sgsHelper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Wyomind\SimpleGoogleShopping\Model\Feeds $sgsModel,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Wyomind\SimpleGoogleShopping\Helper\Parser $parserHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead,
        \Magento\Framework\Module\Dir\Reader $directoryReader
    ) {
    
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->heartbeatHelper = $heartbeatHelper;
        $this->licenseHelper = $licenseHelper;
        $this->cacheManager = $contextModel->getCacheManager();
        $this->sgsHelper = $sgsHelper;
        $this->sgsModel = $sgsModel;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultRawFactory = $resultRawFactory;
        $this->parserHelper = $parserHelper;
        $this->attributeRepository = $attributeRepository;
        $this->feedsCollectionFactory = $feedsCollectionFactory;
        $this->filter = $filter;
        $directory = $directoryReader->getModuleDir('', 'Wyomind_SimpleGoogleShopping');
        $this->directoryRead = $directoryRead->create($directory);
    }

    /**
     * execute action
     */
    abstract public function execute();
}

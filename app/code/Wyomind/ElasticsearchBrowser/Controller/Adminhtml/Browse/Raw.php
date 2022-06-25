<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchBrowser\Controller\Adminhtml\Browse;

/**
 * Class Raw
 */
class Raw extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_ElasticsearchBrowser::browse';

    /**
     * @var \Magento\Framework\View\Result\PageFactory|null
     */
    public $resultPageFactory = null;

    /**
     * @var \Wyomind\ElasticsearchBrowser\Model\Client
     */
    protected $_client = null;

    /**
     * Raw constructor
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Wyomind\ElasticsearchBrowser\Model\Client $client
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Wyomind\ElasticsearchBrowser\Model\Client $client
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        $this->_client = $client;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        return $this->getResponse()->representJson($this->getJsonData());
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getJsonData()
    {
        $indice = $this->getRequest()->getParam('indice');
        $docId = $this->getRequest()->getParam('id');

        $this->_client->init();
        $type = \Magento\Elasticsearch\Model\Config::ELASTICSEARCH_TYPE_DOCUMENT;
        $data = $this->_client->getByIds([$indice], $type, [$docId]);

        return json_encode($data['docs'][0], JSON_PRETTY_PRINT);
    }
}

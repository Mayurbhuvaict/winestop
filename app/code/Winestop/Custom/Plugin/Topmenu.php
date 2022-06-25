<?php 

namespace Winestop\Custom\Plugin;
use Magento\Framework\Data\Tree\NodeFactory;

class Topmenu
{
    /**
     * @var \Magento\Framework\Data\Tree\NodeFactory
     */
    protected $nodeFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param Magento\Framework\Data\Tree\NodeFactory $nodeFactory
     * @param Magento\Cms\Model\PageFactory $pageFactory
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        NodeFactory $nodeFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
    }

    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        $node = $this->nodeFactory->create(
            [
                'data' => $this->getNodeAsArray(),
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        $node1 = $this->nodeFactory->create(
            [
                'data' => $this->getNodeAsArray1(),
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        $node2 = $this->nodeFactory->create(
            [
                'data' => $this->getNodeAsArray2(),
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        $subject->getMenu()->addChild($node);
        $subject->getMenu()->addChild($node1);
        $subject->getMenu()->addChild($node2);
    }

    protected function getNodeAsArray()
    {
        $data = [
            'name' => __('Clearance Wines'),
            'id' => 'clearance-wines',
            'url' => '/wines.html?special_designations=598',
            'has_active' => false,
            'is_active' => false // (expression to determine if menu item is selected or not)
        ];
        return $data;
    }
    
    protected function getNodeAsArray1()
    {
        $data = [
            'name' => __('Events'),
            'id' => 'local-events',
            'url' => '/local-events',
            'has_active' => false,
            'is_active' => false // (expression to determine if menu item is selected or not)
        ];
        return $data;
    }
    
    protected function getNodeAsArray2()
    {
        $data = [
            'name' => __('Recent Offers'),
            'id' => 'recent-offers',
            'url' => '#',
            'has_active' => false,
            'is_active' => false // (expression to determine if menu item is selected or not)
        ];
        return $data;
    }

    protected function getCmspage($identifier) {
        $page = $this->_pageFactory->create();
        $pageId = $page->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
        if (!$pageId) {
            return null;
        }
        $page->setStoreId($this->_storeManager->getStore()->getId());
        if (!$page->load($pageId)) {
            return null;
        }
        if (!$page->getId()) {
            return null;
        }
        return $page;
    }
}
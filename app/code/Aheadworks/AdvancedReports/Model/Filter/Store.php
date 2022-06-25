<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */































































































































































































namespace Aheadworks\AdvancedReports\Model\Filter;

use Aheadworks\AdvancedReports\Model\Filter\Store\Encoder as FilterStoreEncoder ;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Store
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class Store implements FilterInterface
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_rep_store_key';

    /**#@+
     * Store types
     */
    const DEFAULT_TYPE = 'default';
    const WEBSITE_TYPE = 'website';
    const GROUP_TYPE = 'group';
    const STORE_TYPE = 'store';
    /**#@-*/

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FilterStoreEncoder
     */
    protected $filterStoreEncoder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    private $scope;

    /**
     * @param SessionManagerInterface $session
     * @param StoreManagerInterface $storeManager
     * @param FilterStoreEncoder $filterStoreEncoder
     * @param RequestInterface $request
     */
    public function __construct(
        SessionManagerInterface $session,
        StoreManagerInterface $storeManager,
        FilterStoreEncoder $filterStoreEncoder,
        RequestInterface $request
    ) {
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->filterStoreEncoder = $filterStoreEncoder;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (!$this->scope) {
            $reportScope = $this->request->getParam('report_scope');
            if (!empty($reportScope)) {
                $this->scope = $reportScope;
            }

            if ($this->scope) {
                $this->session->setData(self::SESSION_KEY, $this->scope);
                return $this->scope;
            }
            if ($keyFromSession = $this->session->getData(self::SESSION_KEY)) {
                $this->scope = $keyFromSession;
                return $this->scope;
            }
            $this->scope = $this->getDefaultValue();
        }

        return $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return $this->filterStoreEncoder->encode(self::DEFAULT_TYPE, self::DEFAULT_TYPE);
    }

    /**
     * Retrieve store Ids
     *
     * @return int[]|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoreIds()
    {
        $storeIds = null;
        $data = $this->filterStoreEncoder->decode($this->getValue());
        switch ($data[0]) {
            case self::WEBSITE_TYPE:
                $storeIds = $this->storeManager->getWebsite($data[1])->getStoreIds();
                break;
            case self::GROUP_TYPE:
                $storeIds = $this->storeManager->getGroup($data[1])->getStoreIds();
                break;
            case self::STORE_TYPE:
                $storeIds = [$this->storeManager->getStore($data[1])->getId()];
                break;
        }
        return $storeIds;
    }

    /**
     * Retrieve website Id
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebsiteId()
    {
        $websiteId = null;
        $data = $this->filterStoreEncoder->decode($this->getValue());
        switch ($data[0]) {
            case self::DEFAULT_TYPE:
                $websiteId = 0;
                break;
            case self::WEBSITE_TYPE:
                $websiteId = $this->storeManager->getWebsite($data[1])->getId();
                break;
            case self::GROUP_TYPE:
                $websiteId = $this->storeManager->getGroup($data[1])->getWebsiteId();
                break;
            case self::STORE_TYPE:
                $websiteId = $this->storeManager->getStore($data[1])->getWebsiteId();
                break;
        }
        return $websiteId;
    }
}

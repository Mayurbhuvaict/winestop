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



namespace Mirasvit\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Seo\Model\RedirectFactory;
use Mirasvit\Seo\Helper\Redirect;
use Mirasvit\Seo\Helper\Data;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\Seo\Model\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResponseInterface;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CheckUrlObserver implements ObserverInterface
{
    const HOME_PAGE_REDIRECT = 'm__home_page_index_redirect';
    const REDIRECT_CHAIN     = '[redirect_chain]';

    protected $redirectFactory;

    protected $redirectHelper;

    protected $dataHelper;

    protected $request;

    protected $response;

    protected $urlManager;

    protected $storeManager;

    protected $scopeConfig;

    /**
     * @var bool
     * true - decode URLs when redirect from Redirect Manager
     * false - work with URLs "as is"
     */
    protected $_redirectUrlFromDecode = true;

    private $customerSession;

    private $config;

    public function __construct(
        RedirectFactory $redirectFactory,
        Redirect $redirectHelper,
        Data $dataHelper,
        HttpRequest $request,
        HttpResponse $response,
        UrlInterface $urlManager,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        Session $customerSession
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->redirectHelper  = $redirectHelper;
        $this->dataHelper      = $dataHelper;
        $this->request         = $request;
        $this->response        = $response;
        $this->urlManager      = $urlManager;
        $this->storeManager    = $storeManager;
        $this->scopeConfig     = $scopeConfig;
        $this->config          = $config;
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $url = $this->request->getRequestUri();
        $url = urldecode($url);

        if (!isset($_SERVER['REQUEST_URI'])) {
            return;
        }

        $fullUrl = $_SERVER['REQUEST_URI'];
        $fullUrl = urldecode($fullUrl);

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }

        if ($this->request->isAjax()) {
            return;
        }

        if (strpos($this->request->getRequestUri(), 'page_cache') !== false) {
            //prevent for varnish urls such /page_cache/block/esi/blocks/%5B%22catalog.topnav%22%5D/handles/WyJkZW
            return;
        }
        if (strpos($this->request->getRequestUri(), 'paypal') !== false) {
            return;
        }
        if (strpos($this->request->getRequestUri(), 'checkout') !== false) {
            return;
        }
        // prevent Invalid header value detected with Magesolution Pagebuilder
        if (strpos($this->request->getRequestUri(), 'fbuilder') !== false) {
            return;
        }
        // redirect uppercase to lowercase
        $p = strpos($fullUrl, "?");
        if ($p === false) {
            $newUrl = strtolower($fullUrl);
        } else {
            //dont lowercase get params
            $mainUrl = substr($fullUrl, 0, $p);
            $query   = substr($fullUrl, $p);
            $newUrl  = strtolower($mainUrl) . $query;
        }

        if ($this->config->isRedirectToLowercaseEnabled((int)$this->storeManager->getStore()->getStoreId())
            && $fullUrl != $newUrl) {

            $allowedTypes = $this->config->getAllowedLowercasePageTypes((int)$this->storeManager->getStore()->getStoreId());

            $allowed = count($allowedTypes) === 0;
            foreach ($allowedTypes as $type) {
                if (preg_match($type, $this->request->getFullActionName())) {
                    $allowed = true;
                }
            }

            if ($allowed) {
                $this->redirectHelper->redirect($this->response, $newUrl);

                return;
            }
        }

        $this->redirectFromRedirectManagerUrlList($this->response);
        $this->redirectHelper->unsetFlag();

        $urlToRedirect = $this->redirectHelper->getUrlWithCorrectEndSlash($url);

        if ($urlToRedirect != '/' && $url != $urlToRedirect) {
            $this->redirectHelper->redirect($this->response, rtrim($this->urlManager->getBaseUrl(), '/') . $urlToRedirect);
        }

        if (substr($fullUrl, -4, 4) == '?p=1') {
            $this->redirectHelper->redirect($this->response, substr($fullUrl, 0, -4));
        }

        //prevent redirect loop if $fullUrl always contains index.php
        if (in_array(trim($fullUrl, '/'), ['index.php'])
            && !$this->customerSession->getData(self::HOME_PAGE_REDIRECT)) {
            $this->customerSession->setData(self::HOME_PAGE_REDIRECT, 1);
            $this->redirectHelper->redirect($this->response, '/');
        } elseif (!in_array(trim($fullUrl, '/'), ['index.php'])) {
            $this->customerSession->unsetData(self::HOME_PAGE_REDIRECT);
        }

        if (in_array(trim($fullUrl, '/'), ['home', 'index.php/home'])) {
            $this->redirectHelper->redirect($this->response, '/');
        }
    }

    /**
     * Get base url (some stores can drop with error if we use default magento getBaseUrl here)
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getSeoBaseUrl(): string
    {
        $httpHostWithPort = $this->request->getHttpHost(false);
        $httpHostWithPort = explode(':', $httpHostWithPort);
        $httpHost         = isset($httpHostWithPort[0]) ? $httpHostWithPort[0] : '';
        $port             = '';
        if (isset($httpHostWithPort[1])) {
            $defaultPorts = [
                \Magento\Framework\App\Request\Http::DEFAULT_HTTP_PORT,
                \Magento\Framework\App\Request\Http::DEFAULT_HTTPS_PORT,
            ];
            if (!in_array($httpHostWithPort[1], $defaultPorts)) {
                /** Custom port */
                $port = ':' . $httpHostWithPort[1];
            }
        }

        $storeCodeInUrl = '';
        if ($this->storeManager->getStore()->getConfig(\Magento\Store\Model\Store::XML_PATH_STORE_IN_URL)) {
            $storeCodeInUrl = '/' . $this->storeManager->getStore()->getCode() . '/';
        }

        $baseUrl = $this->request->getScheme() . '://' . $httpHost . $port;

        if ($storeCodeInUrl && strpos($this->urlManager->getCurrentUrl(), $baseUrl . $storeCodeInUrl) !== false) {
            $baseUrl = $baseUrl . $storeCodeInUrl;
        }

        if (strpos($this->request->getScheme(), 'https') !== false) {
            $configPath = 'web/secure/base_url';
        } else {
            $configPath = 'web/unsecure/base_url';
        }
        $baseConfigUrl = $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        if (substr_count($baseConfigUrl, "/") > 3) {
            $baseUrl = $baseConfigUrl;
        }

        if (substr($baseUrl, -1) != "/") {
            $baseUrl .= "/";
        }

        return $baseUrl;
    }

    protected function prepareRedirectUrl(string $urlFrom): string
    {
        if (stripos($urlFrom, 'http://') === false
            && stripos($urlFrom, 'https://') === false
        ) {
            return $this->urlManager->getBaseUrl() . ltrim($urlFrom, '/');
        }

        return $urlFrom;
    }

    /**
     * Do redirect using records of our Redirect Manager.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function redirectFromRedirectManagerUrlList(ResponseInterface $response): bool
    {
        \Magento\Framework\Profiler::start(__METHOD__);

        $currentUrl = $this->urlManager->getCurrentUrl();
        if ($this->_redirectUrlFromDecode) {
            $currentUrl = rawurldecode($currentUrl);
        }
        $currentAction = $this->dataHelper->getFullActionCode();
        $baseUrl       = $this->getSeoBaseUrl();

        /** @var \Mirasvit\Seo\Model\ResourceModel\Redirect\Collection $redirectCollection */
        $redirectCollection = $this->redirectFactory->create()
            ->getCollection();
        $redirectCollection
            ->addActiveFilter()
            ->addStoreFilter($this->storeManager->getStore());

        $url        = str_replace($baseUrl, '', $currentUrl);
        $trimmedUrl = str_replace(rtrim($baseUrl, '/'), '', $currentUrl);
        $where      = 'url_from = ' . "'" . addslashes($currentUrl) . "'"
            . ' OR ' . 'url_from = ' . "'" . addslashes($url) . "'"
            . ' OR ' . 'url_from = ' . "'" . addslashes($trimmedUrl) . "'"
            . ' OR ' . "'" . addslashes($url) . "'" . " LIKE CONCAT(REPLACE(url_from, '*', '%'))"
            . ' OR ' . "'" . addslashes($trimmedUrl) . "'" . " LIKE CONCAT(REPLACE(url_from, '*', '%'))"
            . ' OR ' . "'" . addslashes($currentUrl) . "'" . " LIKE CONCAT(REPLACE(url_from, '*', '%'))"
            . ' OR ' . "'" . addslashes($url) . "'"
            . " LIKE CONCAT(REPLACE(url_from, '" . self::REDIRECT_CHAIN . "', '%'))"
            . ' OR ' . "'" . addslashes($trimmedUrl) . "'"
            . " LIKE CONCAT(REPLACE(url_from, '" . self::REDIRECT_CHAIN . "', '%'))"
            . ' OR ' . "'" . addslashes($currentUrl) . "'"
            . " LIKE CONCAT(REPLACE(url_from, '" . self::REDIRECT_CHAIN . "', '%'))";

        $redirectCollection->getSelect()
            ->where(new \Zend_Db_Expr($where), null, \Magento\Framework\DB\Select::TYPE_CONDITION)
            ->order('LENGTH(url_from) DESC');

        foreach ($redirectCollection as $redirect) {
            $urlFrom = $this->prepareRedirectUrl($redirect->getUrlFrom());
            $urlTo   = $this->prepareRedirectUrl($redirect->getUrlTo());
            $action  = $redirect->getIsRedirectOnlyErrorPage();

            if ($action && $currentAction != 'cms_noroute_index') {
                continue;
            }

            if (strpos($urlTo, '[redirect_chain]') !== false) {
                $urlTo   = $this->getRedirectChainUrlTo($urlFrom, $urlTo, $currentUrl);
                $urlFrom = $currentUrl;
            }

            if (!$urlTo) {
                continue;
            }

            // To prevent redirect loop is rule is set up incorrectly
            if ($this->redirectHelper->checkRedirectPattern($redirect->getUrlFrom(), $redirect->getUrlTo(), $action) && $urlFrom == $urlTo) {
                continue;
            }

            if ($this->redirectHelper->checkForLoop($urlTo)) {
                continue;
            }

            if ($currentUrl == $urlFrom
                || (stripos($redirect->getUrlFrom(), '*') !== false
                    && $this->redirectHelper->checkRedirectPattern($redirect->getUrlFrom(), $currentUrl))) {
                $this->redirectHelper->setFlag($currentUrl);
                $this->redirectHelper->redirect($response, $urlTo, $redirect->getRedirectType());
                break;
            }
        }

        \Magento\Framework\Profiler::stop(__METHOD__);

        return false;
    }

    private function getRedirectChainUrlTo(string $urlFrom, string $urlTo, string $currentUrl): ?string
    {
        $urlToPostfix = $this->getUrlToPostfix($urlFrom, $urlTo, $currentUrl);
        if ($urlToPostfix !== false) {
            return str_replace(self::REDIRECT_CHAIN, $urlToPostfix, $urlTo);
        }

        return null;
    }

    private function getUrlToPostfix(string $urlFrom, string $urlTo, string $currentUrl): ?string
    {
        $urlToPostfix  = false;
        $urlToExploded = explode(self::REDIRECT_CHAIN, $urlTo);
        if (strpos($urlFrom, self::REDIRECT_CHAIN) !== false
            && isset($urlToExploded[1]) && !$urlToExploded[1]) {
            $urlFromPrepared = str_replace(self::REDIRECT_CHAIN, '', $urlFrom);
            $urlToPostfix    = str_replace($urlFromPrepared, '', $currentUrl);
        }

        if ((isset($urlToPostfix) && $urlToPostfix
                && strpos($currentUrl, $urlToPostfix) !== false)
            || (isset($urlToPostfix) && $urlToPostfix !== false)) {
            return $urlToPostfix;
        }

        return null;
    }
}

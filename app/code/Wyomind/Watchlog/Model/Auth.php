<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Model;

/**
 * Class Auth
 * @package Wyomind\Watchlog\Model
 * 
 */
class Auth
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|null
     */
    protected $_datetime = null;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request|null
     */
    protected $_request = null;

    /**
     * @var null|AttemptsFactory
     */
    protected $_attemptsFactory = null;

    /**
     * @var null|\Wyomind\Framework\Helper\Module
     */
    protected $_framework = null;

    /**
     * @var null|\Wyomind\Watchlog\Helper\Data
     */
    protected $_watchlogHelper = null;

    /**
     * @var null
     */
    protected $_auth = null;

    /**
     * Auth constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @param AttemptsFactory $attemptsFactory
     * @param \Wyomind\Framework\Helper\Module $framework
     * @param \Wyomind\Watchlog\Helper\Data $watchlogHelper
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Wyomind\Watchlog\Model\AttemptsFactory $attemptsFactory,
        \Wyomind\Framework\Helper\Module $framework,
        \Wyomind\Watchlog\Helper\Data $watchlogHelper
    ) {
    
        $this->_datetime = $datetime;
        $this->_request = $request;
        $this->_attemptsFactory = $attemptsFactory;
        $this->_framework = $framework;
        $this->_watchlogHelper = $watchlogHelper;
    }

    /**
     * @param $ex
     */
    public function throwException($ex)
    {
        $this->_auth->throwException($ex);
    }

    public function aroundLogin(
        \Magento\Backend\Model\Auth $auth,
        \Closure $closure,
        $login,
        $password
    ) {
    
        $this->_auth = $auth;
        $exception = null;
        try {
            $closure($login, $password);
        } catch (\Magento\Backend\Model\Auth\PluginAuthenticationException $e) {
            $exception = $e;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $exception = $e;
        } catch (\Magento\Backend\Model\Auth\AuthenticationException $e) {
            $exception = $e;
        }

        $this->addAttempt($login, $password, $exception);
        if ($exception != null) {
            throw $exception;
        }

        return null;
    }

    /**
     * @param $login
     * @param $password
     * @param null $e
     */
    public function addAttempt(
        $login,
        $password,
        $e = null
    ) {
    
        $data = [
            'login' => $login,
            'ip' => strtok($this->_request->getClientIp(), ','),
            'date' => $this->_datetime->gmtDate('Y-m-d H:i:s'),
            'status' => \Wyomind\Watchlog\Helper\Data::SUCCESS,
            'message' => '',
            'url' => $this->_request->getRequestUri()
        ];

        if ($e != null) { // failed
            $data['status'] = \Wyomind\Watchlog\Helper\Data::FAILURE;
            $data['message'] = $e->getMessage();
        } else { // success
            $this->_watchlogHelper->checkNotification();
        }

        $attempt = $this->_attemptsFactory->create()->load(0);
        $attempt->setData($data);
        $attempt->save();
    }
}

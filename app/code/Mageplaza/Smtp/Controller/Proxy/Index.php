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
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Smtp\Controller\Proxy;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Smtp\Helper\Data;
use Mageplaza\Smtp\Helper\EmailMarketing;

/**
 * Class Index
 * @package Mageplaza\Smtp\Controller\Proxy
 */
class Index extends Action
{
    /**
     * @var EmailMarketing
     */
    protected $helperEmailMarketing;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param EmailMarketing $helperEmailMarketing
     */
    public function __construct(
        Context $context,
        EmailMarketing $helperEmailMarketing
    ) {
        $this->helperEmailMarketing = $helperEmailMarketing;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try {
            $params   = $this->getRequest()->getParams();
            $response = $this->helperEmailMarketing->sendRequestProxy($params);
        } catch (Exception $e) {
            $response = [];
        }

        return $this->getResponse()->representJson(Data::jsonEncode($response));
    }
}

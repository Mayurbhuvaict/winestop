<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller;

/**
 * Simple google shopping frontend controller
 */
abstract class Feeds extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Wyomind\Framework\Helper\License|null
     */
    protected $licenseHelper = null;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory|null
     */
    protected $resultRawFactory = null;

    /**
     * Feeds constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Wyomind\Framework\Helper\License $licenseHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Wyomind\Framework\Helper\License $licenseHelper
    ) {
    
        $this->licenseHelper = $licenseHelper;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

    /**
     * execute action
     */
    abstract public function execute();
}

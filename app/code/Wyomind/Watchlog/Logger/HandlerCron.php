<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Logger;

/**
 * Class HandlerCron
 * @package Wyomind\Watchlog\Logger
 */
class HandlerCron extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    public $fileName = '/var/log/Watchlog-cron.log';
    /**
     * @var int
     */
    public $loggerType = \Monolog\Logger::NOTICE;
}

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


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Service;


use Magento\Framework\Shell\Driver;
use Mirasvit\Core\Service\CompatibilityService;
use Psr\Log\LoggerInterface;

class ServerLoadService
{
    /**
     * @var Driver
     */
    private $driver;

    private $logger;

    public function __construct(
        Driver $driver,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->driver = $driver ?: CompatibilityService::getObjectManager()->get(Driver::class);
    }

    public function getRate(): int
    {
        if ($this->isWin()) {
            $rate = $this->getWinServerLoadFirst() ?: $this->getWinServerLoadSecond();
        } else {
            $rate = sys_getloadavg();

            if (!is_array($rate)) {
                $this->logger->warning('PHP function sys_getloadavg() failed. Unable to check server load');
                $rate = 50;
            } else {
                $rate = max($rate[0], $rate[1]);
                $rate = round($rate / $this->getNumCores() * 100);
            }
        }

        if ($rate > 100) {
            $rate = 50;
        }

        return (int)$rate;
    }

    private function isWin(): bool
    {
        if ('WIN' == strtoupper(substr(PHP_OS, 0, 3))) {
            return true;
        }

        return false;
    }

    private function getWinServerLoadFirst(): ?int
    {
        $load = null;
        if ($output = $this->getOutputFromExecCommand('wmic cpu get', 'loadpercentage /all 2>&1')) {
            $output = explode(PHP_EOL, $output);
        }

        if (!$output) {
            return $load;
        }
        foreach ($output as $line) {
            if ($line && preg_match("/^[0-9]+\$/", $line)) {
                $load = $line;
                break;
            }
        }

        return (int)$load;
    }

    private function getWinServerLoadSecond(): int
    {
        /** @var mixed $wmi */
        $wmi    = new \COM("Winmgmts://");
        $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");

        $cpuNum    = 0;
        $loadTotal = 0;

        foreach ($server as $cpu) {
            $cpuNum++;
            $loadTotal += $cpu->loadpercentage;
        }

        $load = round($loadTotal / $cpuNum);

        return $load;
    }
    
    private function getNumCores(): int
    {
        $num = null;
        if (file_exists('/proc/cpuinfo')
            && is_readable('/proc/cpuinfo')
            && is_file('/proc/cpuinfo')
        ) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');

            preg_match_all('/^processor/m', $cpuinfo, $matches);

            $num = count($matches[0]);
        } elseif ($this->isWin()) {
            $output = $this->getOutputFromExecCommand('wmic cpu get', 'NumberOfCores');

            if (!$output) {
                return 1;
            }

            $num = intval($output);
        } else {
            $output = $this->getOutputFromExecCommand('sysctl', '-a');

            if (!$output) {
                return 1;
            }

            preg_match('/hw.ncpu: (\d+)/', $output, $matches);

            if ($matches) {
                $num = intval($matches[1][0]);
            }
        }

        $num = intval($num);

        return $num ? $num : 1;
    }

    private function getOutputFromExecCommand(string $command, string $arguments): ?string
    {
        $output = null;

        try {
            if (is_executable($command)) {
                $response = $this->driver->execute($command, $arguments);
                $output = $response ? $response->getOutput() : null;
            }
        } catch (\Exception $e) {
            return $output;
        }

        return $output;
    }
}
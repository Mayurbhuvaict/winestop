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


namespace Mirasvit\SeoAudit\Cron;


use Magento\Framework\App\MaintenanceMode;
use Mirasvit\SeoAudit\Service\CheckService;
use Psr\Log\LoggerInterface;

class CheckCron
{
    private $checkServise;
    
    private $maintenanceMode;
    
    private $logger;

    public function __construct(
        CheckService $checkService,
        MaintenanceMode $maintenanceMode,
        LoggerInterface $logger
    ) {
        $this->checkServise    = $checkService;
        $this->maintenanceMode = $maintenanceMode;
        $this->logger          = $logger;
    }

    public function execute(): void
    {
        if ($this->maintenanceMode->isOn()) {
            $this->logger->notice('Maintnance mode enabled. SEO Audit will not perform checks');
            
            return;
        }
        
        $this->checkServise->runChecks();
    }
}

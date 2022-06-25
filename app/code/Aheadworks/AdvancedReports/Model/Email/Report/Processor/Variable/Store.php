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





























































namespace Aheadworks\AdvancedReports\Model\Email\Report\Processor\Variable;

use Aheadworks\AdvancedReports\Model\Config as ModuleConfig;
use Aheadworks\AdvancedReports\Model\Source\Email\Report\EmailVariables;

/**
 * Class Store
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Report\Processor\Variable
 */
class Store implements VariableProcessorInterface
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        $variables[EmailVariables::STORE_NAME] = $this->moduleConfig->getStoreName();

        return $variables;
    }
}

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


























































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column;

use Aheadworks\AdvancedReports\Ui\Component\Listing\Column;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class OtherDiscounts
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column
 */
class OtherDiscounts extends Column
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var array
     */
    private $relatedModules = [
        'Magento_CustomerBalance',
        'Magento_GiftCard',
        'Magento_Reward',
        'Aheadworks_StoreCredit',
        'Aheadworks_RewardPoints',
        'Aheadworks_Giftcard',
        'Aheadworks_Raf',
    ];

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ModuleManager $moduleManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ModuleManager $moduleManager,
        array $components = [],
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        if (!$this->isOtherDiscountsEnabled()) {
            $config['componentDisabled'] = true;
        }

        $this->setData('config', $config);

        return parent::prepare();
    }

    /**
     * Check if other discounts column is enabled
     *
     * @return bool
     */
    private function isOtherDiscountsEnabled()
    {
        foreach ($this->relatedModules as $moduleName) {
            if ($this->moduleManager->isEnabled($moduleName)) {
                return true;
            }
        }

        return false;
    }
}

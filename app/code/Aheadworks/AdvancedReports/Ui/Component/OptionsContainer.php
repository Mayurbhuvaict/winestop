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







































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Ui\Component\Container;

/**
 * Class OptionsContainer
 *
 * @package Aheadworks\AdvancedReports\Ui\Component
 */
class OptionsContainer extends Container
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->prepareOptions();

        parent::prepare();
    }

    /**
     * Prepare options
     *
     * @return $this
     */
    protected function prepareOptions()
    {
        $config = $this->getData('config');
        if (isset($config['options'])) {
            if ($config['options'] instanceof OptionSourceInterface) {
                $config['options'] = $config['options']->toOptionArray();
            }
        }
        if (!isset($config['options']) || !is_array($config['options'])) {
            $config['options'] = [];
        }
        $this->setData('config', $config);

        return $this;
    }
}

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





































































































namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module;

use Magento\Framework\DataObject;

/**
 * Class Expression
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module
 */
class Expression extends DataObject implements ExpressionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        return $this->getData(self::MODULE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setModuleName($name)
    {
        return $this->setData(self::MODULE_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }
}

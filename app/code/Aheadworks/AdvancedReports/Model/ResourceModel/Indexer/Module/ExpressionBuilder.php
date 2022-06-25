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

use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class ExpressionBuilder
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Module
 */
class ExpressionBuilder
{
    /**
     * @var ExpressionInterfaceFactory
     */
    private $expressionFactory;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var array
     */
    private $expressions;

    /**
     * @var string
     */
    private $groupExpression;

    /**
     * @var string
     */
    private $defaultEmptyExpression;

    /**
     * @param ExpressionInterfaceFactory $expressionFactory
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ExpressionInterfaceFactory $expressionFactory,
        ModuleManager $moduleManager
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Add expression
     *
     * @param string $moduleName
     * @param string $value
     * @return $this
     */
    public function addExpression($moduleName, $value)
    {
        /** @var ExpressionInterface $expression */
        $expression = $this->expressionFactory->create();
        $expression
            ->setModuleName($moduleName)
            ->setValue($value);

        $this->expressions[] = $expression;

        return $this;
    }

    /**
     * Set group expression
     *
     * @param string $groupExpresssion
     * @return $this
     */
    public function setGroupExpression($groupExpresssion)
    {
        $this->groupExpression = $groupExpresssion;

        return $this;
    }

    /**
     * Set default empty expression
     *
     * @param string $defaultEmptyExpresssion
     * @return $this
     */
    public function setDefaultEmptyExpression($defaultEmptyExpresssion)
    {
        $this->defaultEmptyExpression = $defaultEmptyExpresssion;

        return $this;
    }

    /**
     * Reset builder
     *
     * @return $this
     */
    public function reset()
    {
        $this->expressions = [];
        $this->groupExpression = null;
        $this->defaultEmptyExpression = null;

        return $this;
    }

    /**
     * Create expression
     *
     * @return string
     */
    public function create()
    {
        $validExpressions = [];
        /** @var ExpressionInterface $expression */
        foreach ($this->expressions as $expression) {
            if ($this->moduleManager->isEnabled($expression->getModuleName())) {
                $validExpressions[] = $expression->getValue();
            }
        }

        $resultExpression = !empty($this->defaultEmptyExpression) ? $this->defaultEmptyExpression : '';
        if (!empty($validExpressions)) {
            $resultExpression = implode(" + ", $validExpressions);
        }

        if (!empty($this->groupExpression)) {
            $result = $this->groupExpression . '(' . $resultExpression . ')';
        } else {
            $result = '(' . $resultExpression . ')';
        }

        $this->reset();

        return $result;
    }
}

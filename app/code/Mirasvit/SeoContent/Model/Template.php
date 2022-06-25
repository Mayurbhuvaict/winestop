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

namespace Mirasvit\SeoContent\Model;

use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class Template extends Content implements TemplateInterface
{
    /**
     * @var Template\Rule
     */
    private $rule;

    private $ruleFactory;

    public function __construct(
        Template\RuleFactory $ruleFactory,
        Context $context,
        Registry $registry
    ) {
        $this->ruleFactory = $ruleFactory;

        parent::__construct($context, $registry);
    }

    protected function _construct(): void
    {
        $this->_init(ResourceModel\Template::class);
    }

    public function setRuleType(int $value): TemplateInterface
    {
        return $this->setData(self::RULE_TYPE, $value);
    }

    public function getRuleType(): ?int
    {
        return $this->getData(self::RULE_TYPE)
            ? (int)$this->getData(self::RULE_TYPE)
            : null;
    }

    public function setName(string $value): TemplateInterface
    {
        return $this->setData(self::NAME, $value);
    }

    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    public function setIsActive(bool $value): TemplateInterface
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    public function isActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setSortOrder(int $value): TemplateInterface
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    public function getSortOrder(): int
    {
        return (int)$this->getData(self::SORT_ORDER);
    }

    public function setStopRuleProcessing(bool $value): TemplateInterface
    {
        return $this->setData(self::STOP_RULE_PROCESSING, $value);
    }

    public function isStopRuleProcessing(): bool
    {
        return (bool)$this->getData(self::STOP_RULE_PROCESSING);
    }

    public function setApplyForChildCategories(bool $value): TemplateInterface
    {
        return $this->setData(self::APPLY_FOR_CHILD_CATEGORIES, $value);
    }

    public function isApplyForChildCategories(): bool
    {
        return (bool)$this->getData(self::APPLY_FOR_CHILD_CATEGORIES);
    }

    public function setConditionsSerialized(string $value): TemplateInterface
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $value);
    }

    public function setStoreIds(array $value): TemplateInterface
    {
        return $this->setData(self::STORE_IDS, implode(',', $value));
    }

    public function getStoreIds(): array
    {
        return explode(',', $this->getData(self::STORE_IDS));
    }

    public function getRule(): Template\Rule
    {
        if (!$this->rule) {
            $this->rule = $this->ruleFactory->create()
                ->setData(self::CONDITIONS_SERIALIZED, $this->getData(self::CONDITIONS_SERIALIZED))
                ->setData(self::ACTIONS_SERIALIZED, $this->getData(self::ACTIONS_SERIALIZED));
        }

        return $this->rule;
    }

    public function setApplyForHomepage(bool $value): TemplateInterface
    {
        return $this->setData(self::APPLY_FOR_HOMEPAGE, $value);
    }

    public function isApplyForHomepage(): bool
    {
        return (bool)$this->getData(self::APPLY_FOR_HOMEPAGE);
    }
}

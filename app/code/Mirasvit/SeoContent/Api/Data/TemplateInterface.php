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

namespace Mirasvit\SeoContent\Api\Data;

use Mirasvit\SeoContent\Model\Template\Rule;

interface TemplateInterface extends ContentInterface
{
    const RULE_TYPE_PRODUCT    = 1;
    const RULE_TYPE_CATEGORY   = 2;
    const RULE_TYPE_NAVIGATION = 3;
    const RULE_TYPE_PAGE       = 4;

    const TABLE_NAME = 'mst_seo_content_template';

    const ID                         = 'template_id';
    const RULE_TYPE                  = 'rule_type';
    const NAME                       = 'name';
    const IS_ACTIVE                  = 'is_active';
    const SORT_ORDER                 = 'sort_order';
    const CONDITIONS_SERIALIZED      = 'conditions_serialized';
    const ACTIONS_SERIALIZED         = 'actions_serialized';
    const STOP_RULE_PROCESSING       = 'stop_rules_processing';
    const APPLY_FOR_CHILD_CATEGORIES = 'apply_for_child_categories';
    const APPLY_FOR_HOMEPAGE         = 'apply_for_homepage';
    const STORE_IDS                  = 'store_ids';

    /**
     * @return int
     */
    public function getId();

    public function setRuleType(int $value): self;

    public function getRuleType(): ?int;

    public function setName(string $value): self;

    public function getName(): string;

    public function setIsActive(bool $value): self;

    public function isActive(): bool;

    public function setSortOrder(int $value): self;

    public function getSortOrder(): int;

    public function setStopRuleProcessing(bool $value): self;

    public function isStopRuleProcessing(): bool;

    public function setApplyForChildCategories(bool $value): self;

    public function isApplyForChildCategories(): bool;

    public function setConditionsSerialized(string $value): self;

    public function setStoreIds(array $value): self;

    public function getStoreIds(): array;

    public function getRule(): Rule;

    public function setApplyForHomepage(bool $value): self;

    public function isApplyForHomepage(): bool;
}

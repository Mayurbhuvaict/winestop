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

interface ContentInterface
{
    const DESCRIPTION_POSITION_DISABLED                = 0;
    const DESCRIPTION_POSITION_BOTTOM_PAGE             = 1;
    const DESCRIPTION_POSITION_UNDER_SHORT_DESCRIPTION = 2;
    const DESCRIPTION_POSITION_UNDER_FULL_DESCRIPTION  = 3;
    const DESCRIPTION_POSITION_UNDER_PRODUCT_LIST      = 4;
    const DESCRIPTION_POSITION_CUSTOM_TEMPLATE         = 5;

    const TITLE                = 'title';
    const META_TITLE           = 'meta_title';
    const META_KEYWORDS        = 'meta_keywords';
    const META_DESCRIPTION     = 'meta_description';
    const META_ROBOTS          = 'meta_robots';
    const DESCRIPTION          = 'description';
    const DESCRIPTION_POSITION = 'description_position';
    const DESCRIPTION_TEMPLATE = 'description_template';
    const SHORT_DESCRIPTION    = 'short_description';
    const FULL_DESCRIPTION     = 'full_description';
    const CATEGORY_DESCRIPTION = 'category_description';
    const CATEGORY_IMAGE       = 'category_image';
    const APPLIED_TEMPLATE_ID  = 'applied_template_id';
    const APPLIED_REWRITE_ID   = 'applied_rewrite_id';

    public function setTitle(string $value): self;

    public function getTitle(): string;

    public function setMetaTitle(string $value): self;

    public function getMetaTitle(): string;

    public function setMetaKeywords(string $value): self;

    public function getMetaKeywords(): string;

    public function setMetaDescription(string $value): self;

    public function getMetaDescription(): string;

    public function setDescription(string $value): self;

    public function getDescription(): string;

    public function setDescriptionPosition(int $value): self;

    public function getDescriptionPosition(): int;

    public function setDescriptionTemplate(string $value): self;

    public function getDescriptionTemplate(): string;

    public function setShortDescription(string $value): self;

    public function getShortDescription(): string;

    public function setFullDescription(string $value): self;

    public function getFullDescription(): string;

    public function setCategoryDescription(string $value): self;

    public function getCategoryDescription(): string;

    public function setCategoryImage(string $value): self;

    public function getCategoryImage(): string;

    public function setMetaRobots(string $value): self;

    public function getMetaRobots(): string;
}

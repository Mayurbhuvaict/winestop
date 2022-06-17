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

use Magento\Framework\Model\AbstractModel;
use Mirasvit\SeoContent\Api\Data\ContentInterface;

class Content extends AbstractModel implements ContentInterface
{
    public function setTitle(string $value): ContentInterface
    {
        return $this->setData(self::TITLE, $value);
    }

    public function getTitle(): string
    {
        return (string)$this->getData(self::TITLE);
    }

    public function setMetaTitle(string $value): ContentInterface
    {
        return $this->setData(self::META_TITLE, $value);
    }

    public function getMetaTitle(): string
    {
        return (string)$this->getData(self::META_TITLE);
    }

    public function setMetaKeywords(string $value): ContentInterface
    {
        return $this->setData(self::META_KEYWORDS, $value);
    }

    public function getMetaKeywords(): string
    {
        return (string)$this->getData(self::META_KEYWORDS);
    }

    public function setMetaDescription(string $value): ContentInterface
    {
        return $this->setData(self::META_DESCRIPTION, $value);
    }

    public function getMetaDescription(): string
    {
        return (string)$this->getData(self::META_DESCRIPTION);
    }

    public function setDescription(string $value): ContentInterface
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    public function getDescription(): string
    {
        return (string)$this->getData(self::DESCRIPTION);
    }

    public function setDescriptionPosition(int $value): ContentInterface
    {
        return $this->setData(self::DESCRIPTION_POSITION, $value);
    }

    public function getDescriptionPosition(): int
    {
        return (int)$this->getData(self::DESCRIPTION_POSITION);
    }

    public function setDescriptionTemplate(string $value): ContentInterface
    {
        return $this->setData(self::DESCRIPTION_TEMPLATE, $value);
    }

    public function getDescriptionTemplate(): string
    {
        return (string)$this->getData(self::DESCRIPTION_TEMPLATE);
    }

    public function setShortDescription(string $value): ContentInterface
    {
        return $this->setData(self::SHORT_DESCRIPTION, $value);
    }

    public function getShortDescription(): string
    {
        return (string)$this->getData(self::SHORT_DESCRIPTION);
    }

    public function setFullDescription(string $value): ContentInterface
    {
        return $this->setData(self::FULL_DESCRIPTION, $value);
    }

    public function getFullDescription(): string
    {
        return (string)$this->getData(self::FULL_DESCRIPTION);
    }

    public function setCategoryDescription(string $value): ContentInterface
    {
        return $this->setData(self::CATEGORY_DESCRIPTION, $value);
    }

    public function getCategoryDescription(): string
    {
        return (string)$this->getData(self::CATEGORY_DESCRIPTION);
    }

    public function setCategoryImage(string $value): ContentInterface
    {
        return $this->setData(self::CATEGORY_IMAGE, $value);
    }

    public function getCategoryImage(): string
    {
        return (string)$this->getData(self::CATEGORY_IMAGE);
    }

    public function setMetaRobots(string $value): ContentInterface
    {
        return $this->setData(self::META_ROBOTS, $value);
    }

    public function getMetaRobots(): string
    {
        return (string)$this->getData(self::META_ROBOTS);
    }
}

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

namespace Mirasvit\SeoContent\Service;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;
use Magento\Framework\Registry;

class TemplateService
{
    private $templateRepository;

    private $storeManager;

    private $registry;

    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        StoreManagerInterface $storeManager,
        Registry $registry
    ) {
        $this->templateRepository = $templateRepository;
        $this->storeManager       = $storeManager;
        $this->registry           = $registry;
    }

    public function getTemplate(
        int $ruleType,
        ?CategoryInterface $category,
        ?ProductInterface $product,
        ?DataObject $filterData
    ): ?TemplateInterface {
        $collection = $this->templateRepository->getCollection();
        $collection->addFieldToFilter(TemplateInterface::IS_ACTIVE, true)
            ->addFieldToFilter(TemplateInterface::RULE_TYPE, $ruleType)
            ->addStoreFilter($this->storeManager->getStore())
            ->setOrder(TemplateInterface::SORT_ORDER, 'desc');

        foreach ($collection as $template) {
            $isFollowRules = true;

            if ($category) {
                $categoryData  = $category->getData();
                $isFollowRules = $isFollowRules && $template->getRule()->validate($category);
                $category->setData($categoryData);
                if (!$isFollowRules && $template->isApplyForChildCategories()) {
                    $parent = $category->getParentCategory();

                    $counter = 0;
                    while ($parent
                        && $parent->getParentId() > 0
                        && !$isFollowRules
                        && $counter < 5
                    ) {
                        $isFollowRules = $template->getRule()->validate($parent);
                        $parent        = $parent->getParentCategory();
                        $counter++;
                    }
                }
            }

            if ($product) {
                $isFollowRules = $isFollowRules && $template->getRule()->validate($product);
                if ($template->isApplyForChildCategories() && !$isFollowRules) {
                    $this->registry->register('apply_for_child_categories', true);
                    $isFollowRules = $template->getRule()->validate($product);
                    $this->registry->unregister('apply_for_child_categories');
                }
            }

            if ($filterData) {
                $isFollowRules = $isFollowRules && $template->getRule()->validate($filterData);
            }

            if ($isFollowRules) {
                return $template;
            }
        }

        return null;
    }
}

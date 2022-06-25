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



namespace Mirasvit\SeoContent\Ui\Template\Form;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * DataProvider constructor.
     * @param TemplateRepositoryInterface $templateRepository
     * @param StoreManagerInterface $storeManager
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        StoreManagerInterface $storeManager,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection   = $templateRepository->getCollection();
        $this->storeManager = $storeManager;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $item) {
            $result[$item->getId()] = [
                TemplateInterface::ID         => $item->getId(),
                TemplateInterface::RULE_TYPE  => (string)$item->getRuleType(),
                TemplateInterface::NAME       => $item->getName(),
                TemplateInterface::IS_ACTIVE  => $item->isActive() ? '1' : '0',
                TemplateInterface::SORT_ORDER => $item->getSortOrder(),
                TemplateInterface::STORE_IDS  => $item->getStoreIds(),

                TemplateInterface::TITLE            => $item->getTitle(),
                TemplateInterface::META_TITLE       => $item->getMetaTitle(),
                TemplateInterface::META_KEYWORDS    => $item->getMetaKeywords(),
                TemplateInterface::META_DESCRIPTION => $item->getMetaDescription(),

                TemplateInterface::DESCRIPTION          => $item->getDescription(),
                TemplateInterface::SHORT_DESCRIPTION    => $item->getShortDescription(),
                TemplateInterface::FULL_DESCRIPTION     => $item->getFullDescription(),
                TemplateInterface::DESCRIPTION_POSITION => $item->getDescriptionPosition(),
                TemplateInterface::DESCRIPTION_TEMPLATE => $item->getDescriptionTemplate(),
                TemplateInterface::CATEGORY_DESCRIPTION => $item->getCategoryDescription(),

                TemplateInterface::STOP_RULE_PROCESSING       => $item->isStopRuleProcessing() ? '1' : '0',
                TemplateInterface::APPLY_FOR_CHILD_CATEGORIES => $item->isApplyForChildCategories() ? '1' : '0',
                TemplateInterface::APPLY_FOR_HOMEPAGE         => $item->isApplyForHomepage() ? '1' : '0',
            ];

            if ($item->getCategoryImage()) {
                $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/category/'
                    . $item->getCategoryImage();

                $result[$item->getId()][TemplateInterface::CATEGORY_IMAGE] = [
                    [
                        'name' => $item->getCategoryImage(),
                        'url'  => $url,
                        'size' => '100',
                        'type' => 'image',
                    ],
                ];
            }
        }

        return $result;
    }
    //
    //    /**
    //     * @param array $data
    //     * @return array
    //     */
    //    private function prepareImageData($data, $imageKey)
    //    {
    //        if (isset($data[$imageKey])) {
    //            $imageName = $data[$imageKey];
    //            unset($data[$imageKey]);
    //            if ($this->imageFile->isExist($imageName)) {
    //                $stat = $this->imageFile->getStat($imageName);
    //                $data[$imageKey] = [
    //                    [
    //                        'name' => $imageName,
    //                        'url'  => $this->imageService->getCategoryImageUrl($imageName),
    //                        'size' => isset($stat) ? $stat['size'] : 0,
    //                        'type' => $this->imageFile->getMimeType($imageName),
    //                    ],
    //                ];
    //            }
    //        }
    //        return $data;
    //    }
}

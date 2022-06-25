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



namespace Mirasvit\SeoMarkup\Block\Rs\Product;

use Magento\Review\Model\Rating;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class RatingData
{
    /** @var \Magento\Catalog\Model\Product */
    private $product;

    /** @var \Magento\Store\Model\Store */
    private $store;

    /**
     * @var ProductConfig
     */
    private $productConfig;

    /**
     * @var ReviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var Rating
     */
    private $rating;

    /**
     * RatingData constructor.
     * @param ProductConfig $productConfig
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param Rating $rating
     */
    public function __construct(
        ProductConfig $productConfig,
        ReviewCollectionFactory $reviewCollectionFactory,
        Rating $rating
    ) {
        $this->productConfig           = $productConfig;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->rating                  = $rating;
    }

    /**
     * @param object $product
     * @param object $store
     * @return array|false
     */
    public function getData($product, $store)
    {
        $this->product = $product;
        $this->store   = $store;

        $data = $this->getJsonData();

        return $data ? $data : false;
    }


    /**
     * @return bool|array
     */
    public function getJsonData()
    {
        if (!$this->product) {
            return null;
        }

        $collection = $this->reviewCollectionFactory->create()
            ->addStatusFilter(Review::STATUS_APPROVED)
            ->addEntityFilter('product', $this->product->getId())
            ->addStoreFilter($this->store->getId())
            ->setDateOrder();

        $data = [];

        if (count($collection)) {
            $ratingValue = 0;
            $ratingCount = 0;

            /** @var Review $review */
            foreach ($collection as $review) {
                $summary = $this->rating->getReviewSummary($review->getId());

                if ($summary->getSum() && $summary->getCount()) {
                    $ratingValue += $summary->getSum() / $summary->getCount() / 20;
                    $ratingCount += 1;
                }
            }

            if ($ratingCount && $ratingValue) {
                $data = [
                    "@type"       => "AggregateRating",
                    "ratingValue" => number_format($ratingValue / $ratingCount, 2),
                    "ratingCount" => $ratingCount,
                    "bestRating"  => 5,
                    "reviewCount" => $collection->getSize(),
                ];
            }
        }

        return $data;
    }
}

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

class ReviewData
{
    private $product;

    private $store;

    private $productConfig;

    private $reviewCollectionFactory;

    private $rating;

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

        if (!$this->productConfig->isIndividualReviewsEnabled()) {
            return false;
        }
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
            ->addStoreFilter($this->store->getStoreId())
            ->setDateOrder();


        $data = [];

        if (count($collection)) {
            /** @var Review $review */
            foreach ($collection as $review) {
                $reviewData = [
                    '@context'      => 'http://schema.org/',
                    '@type'         => 'Review',
                    'name'          => $review->getData('title'),
                    'datePublished' => $review->getCreatedAt(),
                    'reviewBody'    => strip_tags($review->getData('detail')),
                    'itemReviewed'  => [
                        '@type' => 'Thing',
                        'name'  => $this->product->getName(),
                    ],
                    'author'        => [
                        '@type' => 'Person',
                        'name'  => $review->getData('nickname'),
                    ],
                    'publisher'     => [
                        '@type' => 'Organization',
                        'name'  => $this->store->getFrontendName(),
                    ],
                ];

                $summary = $this->rating->getReviewSummary($review->getId());

                if ($summary->getSum()) {
                    $ratingValue = number_format($summary->getSum() / $summary->getCount() / 20, 2);

                    $reviewData['reviewRating'] = [
                        '@type'       => 'Rating',
                        'ratingValue' => $ratingValue,
                    ];
                }

                $data[] = $reviewData;
            }
        }

        return $data;
    }
}

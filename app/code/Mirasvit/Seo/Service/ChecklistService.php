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



namespace Mirasvit\Seo\Service;

use Mirasvit\Seo\Service\Checklist\ChecklistTestsService;

class ChecklistService
{
    private $tests
                       = [
            'robotsTxtIndexable'         => 'robots.txt is allowed for robots',
            'homepageMetaTagsExists'     => 'Homepage meta tags check',
            'robotsTxtSEOSitemapExists'  => 'robots.txt contains SEO Sitemap',
            'GoogleAnalyticsTagApplied'  => 'Google Analytics Tag applied',
            'HTMLSitemapExists'          => 'HTML Sitemap exists',
            'ProductsRSExists'           => 'Rich Snippets are enabled for Products',
            'CategoriesRSExists'         => 'Rich Snippets are enabled for Categories',
            'SEOFriendlyImageURLEnabled' => 'SEO Friendly Image URLs are enabled',
            'SEOFriendlyAltEnabled'      => 'Product Images Alt and Title generation enabled',
        ];

    private $checklistTestsService;

    private $results   = [];

    private $totalQty  = 0;

    private $passedQty = 0;

    private $failedQty = 0;

    public function __construct(
        ChecklistTestsService $checklistTestsService
    ) {
        $this->checklistTestsService = $checklistTestsService;
    }

    public function runTests()
    {
        if (empty($this->results)) {
            foreach ($this->tests as $key => $name) {
                $results = $this->checklistTestsService->resolve($key . 'Test');

                $row = [
                    'name'     => $name,
                    'hint'     => $this->checklistTestsService->resolve($key . 'Hint'),
                    'status'   => $results['status'],
                    'solution' => $results['message'],
                ];


                $results['status'] ? $this->passedQty++ : $this->failedQty++;

                $this->results[] = $row;
            }

            $this->totalQty = count($this->tests);
        }

        return $this->results;
    }

    /**
     * @return int
     */
    public function getTotalQty()
    {
        if (empty($this->results)) {
            $this->runTests();
        }

        return $this->totalQty;
    }

    /**
     * @return int
     */
    public function getPassedQty()
    {
        if (empty($this->results)) {
            $this->runTests();
        }

        return $this->passedQty;
    }

    /**
     * @return int
     */
    public function getFailedQty()
    {
        if (empty($this->results)) {
            $this->runTests();
        }

        return $this->failedQty;
    }
}

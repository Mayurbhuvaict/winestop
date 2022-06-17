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



namespace Mirasvit\SeoMarkup\Plugin\Frontend\Theme\Block\Html\Breadcrumbs;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Mirasvit\SeoMarkup\Model\Config\BreadcrumbListConfig;

class MemorizeCrumbsPlugin
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * MemorizeCrumbsPlugin constructor.
     * @param UrlInterface $urlBuilder
     * @param Registry $registry
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Registry $registry
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry   = $registry;
    }


    /**
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterToHtml($subject, $result)
    {
        $crumbs = $this->parseHtml($result);

        $this->registry->register(BreadcrumbListConfig::REGISTER_KEY, $crumbs, true);

        return $result;
    }


    /**
     * @param string $html
     * @return array
     */
    private function parseHtml($html)
    {
        $result = [];

        preg_match_all('/\\<li(.*?)\\/li\\>/ims', $html, $liTagResult);

        if (isset($liTagResult[0]) && count($liTagResult[0]) > 1) {
            foreach ($liTagResult[0] as $line) {
                $item = $this->parseCrumbLine($line);

                if ($item) {
                    $result[$item['url']] = $item['label'];
                }
            }
        }

        return $result;
    }

    /**
     * @param string $line
     * @return array|false
     */
    private function parseCrumbLine($line)
    {
        if (strpos($line, '</a>') !== false) {
            preg_match_all('/\\<a(.*?)\\/a\\>/ims', $line, $aTagResult);

            if (isset($aTagResult[0])) {
                preg_match_all('/href="(.*?)"/ims', implode(' ', $aTagResult[0]), $links);
                preg_match_all('/\\>(.*?)\\<\\/a\\>/ims', implode(' ', $aTagResult[0]), $title);

                if (isset($links[1][0]) && isset($title[1][0])) {
                    return [
                        'url'   => $links[1][0],
                        'label' => $title[1][0],
                    ];
                }
            }
        } else {
            return [
                'url'   => $this->urlBuilder->getCurrentUrl(),
                'label' => strip_tags($line),
            ];
        }

        return false;
    }
}

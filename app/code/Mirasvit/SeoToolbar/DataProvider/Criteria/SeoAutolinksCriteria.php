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



namespace Mirasvit\SeoToolbar\DataProvider\Criteria;

use Mirasvit\SeoToolbar\Api\Data\DataProviderItemInterface;

class SeoAutolinksCriteria extends AbstractCriteria
{
    const LABEL = 'SEO Cross Links';

    /**
     * @param string $content
     * @return \Magento\Framework\DataObject|mixed
     */
    public function handle($content)
    {
        $autoLinks = $this->getAutoLinks($content);

        if (!$autoLinks) {
            return $this->getItem(self::LABEL, DataProviderItemInterface::STATUS_NONE, __('No cross links detected'), '');
        } else {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_SUCCESS,
                __('Cross Links successfully applied'),
                implode(PHP_EOL, $autoLinks)
            );
        }
    }

    /**
     * @param string $content
     * @return array
     */
    private function getAutoLinks($content)
    {
        $result = [];
        $counter = [];

        $matches = [];
        preg_match_all("/<a class='mst_seo_autolink(.*)[^>]+>(.*)<\/a>/iU", $content, $matches);

        if (empty($matches)) {
            return false;
        } else {
            foreach ($matches[0] as $autoLinkTag) {
                preg_match('/>(.*)</', $autoLinkTag, $autoLinkTitle);
                preg_match_all('/(data-page-limit|data-link-limit)="([^"]*)"/i', $autoLinkTag, $autoLinkData);
                $tmpData = [];
                $tmpData = [];

                foreach ($autoLinkData[0] as $data) {
                    $data = str_replace('data-page-limit', 'Page limit', $data);
                    $data = str_replace('data-link-limit', 'Links limit', $data);
                    $tmpData[] = $data;
                }

                if (!isset($counter[$autoLinkTitle[1]])) {
                    $counter[$autoLinkTitle[1]] = 1 ;
                } else {
                    $counter[$autoLinkTitle[1]] += 1 ;
                }

                $result[$autoLinkTitle[1]] = '"'. $autoLinkTitle[1] .'" title : '. implode(',', $tmpData);
            }
        }

        foreach ($counter as $key => $qty) {
            if ($qty > 1) {
                $result[$key] .= ', ' . $qty .' replacements';
            }
        }

        return $result;
    }
}

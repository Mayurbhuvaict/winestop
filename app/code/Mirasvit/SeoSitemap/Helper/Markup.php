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



namespace Mirasvit\SeoSitemap\Helper;

use Magento\Sitemap\Model\Sitemap;

class Markup extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @return array
     */
    public function getTagsData()
    {
        return [Sitemap::TYPE_INDEX => [
                Sitemap::OPEN_TAG_KEY  => '<?xml version="1.0" encoding="UTF-8"?>' .
                    PHP_EOL .
                    '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' .
                    PHP_EOL,
                Sitemap::CLOSE_TAG_KEY => '</sitemapindex>',
            ],
            Sitemap::TYPE_URL   => [
                Sitemap::OPEN_TAG_KEY  => '<?xml version="1.0" encoding="UTF-8"?>' .
                    PHP_EOL .
                    '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' .
                    ' xmlns:content="http://www.google.com/schemas/sitemap-content/1.0"' .
                    ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' .
                    PHP_EOL,
                Sitemap::CLOSE_TAG_KEY => '</urlset>',
            ],
        ];
    }

    /**
     * @param string $url
     * @param string $title
     * @param string $caption
     * @return string
     */
    public function getImageMarkup($url, $title, $caption)
    {
        if (!$caption) {
            $caption = $title;
        }
        $imageMarkup = '<image:image>
                            <image:loc>'     . $url        .'</image:loc>
                            <image:title>'   . $title      .'</image:title>
                            <image:caption>' . $caption    . '</image:caption>
                        </image:image>';
        return $imageMarkup;
    }

    /**
     * @param string $title
     * @param string $url
     * @param string $alt
     * @return string
     */
    public function afterGetImageMarkup($title, $url, $alt)
    {
        return '<PageMap xmlns="http://www.google.com/schemas/sitemap-pagemap/1.0"><DataObject type="thumbnail">
            <Attribute name="name" value="'. $title .'"/>
            <Attribute name="src" value="'. $url .'"/>
            <Attribute name="alt" value="'. $alt .'"/>
            </DataObject></PageMap>';
    }
}

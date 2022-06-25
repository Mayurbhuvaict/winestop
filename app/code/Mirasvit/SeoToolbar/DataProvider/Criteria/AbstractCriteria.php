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

use Magento\Framework\DataObject;

abstract class AbstractCriteria
{
    /**
     * @param string $content
     * @return mixed
     */
    abstract public function handle($content);

    /**
     * @param string $title
     * @param array $status
     * @param string $description
     * @param string $note
     * @param string $action
     * @return DataObject
     */
    protected function getItem($title, $status, $description, $note, $action = '')
    {
        return new DataObject([
            'title'       => $title,
            'status'      => $status,
            'description' => $description,
            'note'        => $note,
            'action'      => $action,
        ]);
    }

    /**
     * @param string $content
     * @param string $tag
     *
     * @return string|false
     */
    protected function getMetaTag($content, $tag)
    {
        $meta = [];

        $pattern
            = '
              ~<\s*meta\s
                (?=[^>]*?
                \b(?:name|property|http-equiv)\s*=\s*
                (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
                ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
              )
              [^>]*?\bcontent\s*=\s*
                (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
                ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
              [^>]*>
              ~ix
        ';

        if (preg_match_all($pattern, $content, $out)) {
            $meta = array_combine($out[1], $out[2]);
        }

        if (preg_match('/<title>(.*?)<\/title>/ims', $content, $out)) {
            $meta['title'] = $out[1];
        }

        preg_match_all('/<link\s*rel="canonical"\s*href="(.*?)"\s*\/>/', $content, $matches);

        if (isset($matches[1][0])) {
            $meta['canonical'] = $matches[1][0];
        }

        return isset($meta[$tag]) ? $meta[$tag] : false;
    }
}

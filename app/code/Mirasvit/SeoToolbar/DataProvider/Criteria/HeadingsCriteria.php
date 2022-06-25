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

class HeadingsCriteria extends AbstractCriteria
{
    const LABEL = 'Headings';

    /**
     * @param string $content
     * @return \Magento\Framework\DataObject
     */
    public function handle($content)
    {
        $tags = [];

        $pattern = '/<(h[1-6]).*?>(.*?)<\/h[1-6]>/ims';
        $content = str_replace('&lt;', '<', $content);
        $content = str_replace('&gt;', '>', $content);

        preg_match_all($pattern, $content, $tags);

        $headers = [
            'h1' => [],
            'h2' => [],
            'h3' => [],
            'h4' => [],
            'h5' => [],
            'h6' => [],
        ];

        if (isset($tags[0])) {
            foreach ($tags[0] as $idx => $tag) {
                $headers[$tags[1][$idx]][] = strip_tags($tag);
            }
        }

        $note = $this->getNote($headers);

        $h1Count = count($headers['h1']);

        if ($h1Count == 0) {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_ERROR,
                __('There is no H1 tag on the page.'),
                $note
            );
        } elseif ($h1Count == 1) {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_SUCCESS,
                __('One H1 tag — optimal.'),
                $note
            );
        } else {
            return $this->getItem(
                self::LABEL,
                DataProviderItemInterface::STATUS_WARNING,
                __('Few H1 tags. Try to use one H1 tag.'),
                $note
            );
        }
    }

    /**
     * @param array $headers
     * @return string
     */
    private function getNote(array $headers)
    {
        $count = [];
        $texts = [];

        foreach ($headers as $header => $items) {
            $count[] = __('<%1>: %2', strtoupper($header), count($items));

            foreach ($items as $item) {
                $item    = str_replace(["\n", "\r"], '', $item);
                $texts[] = __('<%1>: %2', strtoupper($header), $item);
            }
        }

        $note = implode('  ', $count) . PHP_EOL . implode(PHP_EOL, $texts);

        return $note;
    }
}

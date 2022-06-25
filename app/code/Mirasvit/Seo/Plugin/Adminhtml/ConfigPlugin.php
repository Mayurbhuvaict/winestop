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


namespace Mirasvit\Seo\Plugin\Adminhtml;


class ConfigPlugin
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function aroundSave(
        \Magento\Config\Model\Config $config,
        \Closure $proceed
    ) {
        if ($config->getData('section') == 'seo') {
            $data = $config->getData('groups');

            if (isset($data['image'])) {
                $fieldsData = $data['image']['fields'];

                if (isset($fieldsData['image_url_template']) && isset($fieldsData['image_url_template']['value'])) {
                    $value = $fieldsData['image_url_template']['value'];

                    preg_match_all('/\[\w*\]/', $value, $match);

                    foreach ($match[0] as $m) {
                        if (strpos($m, '[page_') !== false) {
                            throw new \Exception(
                                'Page variables are not allowed for the "Template for URL key of Product Images" field.'
                            );
                        }
                    }
                }

                if (isset($fieldsData['image_alt_template']) && isset($fieldsData['image_alt_template']['value'])) {
                    $value = $fieldsData['image_alt_template']['value'];

                    preg_match_all('/\[\w*\]/', $value, $match);

                    foreach ($match[0] as $m) {
                        if (strpos($m, '[page_') !== false) {
                            throw new \Exception(
                                'Page variables are not allowed for the "Template for Product Images Alt and Title" field.'
                            );
                        }
                    }
                }
            }
        }

        return $proceed();
    }
}

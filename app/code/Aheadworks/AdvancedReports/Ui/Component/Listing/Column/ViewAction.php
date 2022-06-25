<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */

























































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column;

/**
 * Class ViewAction
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column
 */
class ViewAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
            $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'id';
            $urlEntityParamValue = $this->getData('config/urlEntityParamValue') ?: 'id';
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$urlEntityParamValue]) && $item[$urlEntityParamValue]) {
                    $item['row_url_' . $fieldName] = $this->context->getUrl(
                        $viewUrlPath,
                        [$urlEntityParamName => $item[$urlEntityParamValue]]
                    );
                } else {
                    $item['row_url_' . $fieldName] = '';
                }
                $item['row_label_' . $fieldName] = $item[$fieldName];
            }
        }
        return $dataSource;
    }
}

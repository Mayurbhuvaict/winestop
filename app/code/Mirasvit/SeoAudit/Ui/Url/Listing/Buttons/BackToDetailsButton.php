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


namespace Mirasvit\SeoAudit\Ui\Url\Listing\Buttons;


use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mirasvit\SeoAudit\Api\Data\CheckResultInterface;

class BackToDetailsButton implements ButtonProviderInterface
{
    private $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    public function getButtonData(): array
    {
        $id = $this->context->getRequest()->getParam(CheckResultInterface::JOB_ID);

        $data = [
            'label' => __('Back to all issues'),
            'id' => 'back-to-details-button',
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/details', [CheckResultInterface::JOB_ID => $id])),
        ];

        return $data;
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}

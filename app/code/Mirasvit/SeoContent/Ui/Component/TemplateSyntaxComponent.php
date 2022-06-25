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

namespace Mirasvit\SeoContent\Ui\Component;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;

class TemplateSyntaxComponent extends AbstractComponent
{
    private $templateEngineService;

    public function __construct(
        TemplateEngineServiceInterface $templateEngineService,
        ContextInterface $context,
        UrlInterface $url,
        array $components = [],
        array $data = []
    ) {
        $this->templateEngineService = $templateEngineService;

        $data['config']['component']  = 'Mirasvit_SeoContent/js/component/template-syntax';
        $data['config']['suggestUrl'] = $url->getUrl('seo_content/template/suggest');

        parent::__construct($context, $components, $data);
    }

    public function getComponentName(): string
    {
        return 'template_syntax';
    }

    public function prepare(): void
    {
        parent::prepare();

        $config = $this->getData('config');

        foreach ($this->templateEngineService->getData() as $scope => $dataObject) {
            $scopeData = [
                'label' => (string)$dataObject->getTitle(),
            ];
            foreach ($dataObject->getVariables() as $var) {
                $scopeData['vars'][] = $scope . '_' . $var;
            }

            $config['scopeData'][] = $scopeData;
        }

        $this->setData('config', $config);
    }
}

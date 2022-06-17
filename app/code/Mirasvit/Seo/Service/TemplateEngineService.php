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

namespace Mirasvit\Seo\Service;

use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\Seo\Service\TemplateEngine\TemplateProcessor;

class TemplateEngineService implements TemplateEngineServiceInterface
{
    private $templateProcessor;

    public function __construct(
        TemplateProcessor $templateProcessor
    ) {
        $this->templateProcessor = $templateProcessor;
    }

    public function render(string $template = null, array $vars = []): string
    {
        return $this->templateProcessor->process((string)$template, $vars);
    }

    /**
     * @return TemplateEngine\Data\AbstractData[]
     */
    public function getData(): array
    {
        return $this->templateProcessor->getData();
    }
}
